(function () {
    'use strict';

    var container = document.getElementById('chat-app');
    if (!container) {
        return;
    }

    var pollUrl = container.dataset.pollUrl;
    var storeUrl = container.dataset.storeUrl;
    var lastId = parseInt(container.dataset.lastId || '0', 10) || 0;
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
    var messagesEl = document.querySelector('.chat-messages .messages');
    var chatBodyEl = document.querySelector('.chat-messages .chat-body');
    var statusTextEl = document.querySelector('.chat-header .last-seen');
    var statusAvatarEl = document.querySelector('.chat-header figure.avatar');
    var pollTimer = null;

    function scrollToBottom() {
        if (chatBodyEl) {
            chatBodyEl.scrollTop = chatBodyEl.scrollHeight;
        }
    }

    function clearEmptyState() {
        if (messagesEl && messagesEl.querySelector('.py-5')) {
            messagesEl.innerHTML = '';
        }
    }

    function renderMessage(message) {
        clearEmptyState();

        var chats = document.createElement('div');
        chats.className = 'chats' + (message.is_mine ? ' chats-right' : '');

        var content = document.createElement('div');
        content.className = 'chat-content';

        var msgContent = document.createElement('div');
        msgContent.className = 'message-content';

        if (message.is_image && message.attachment_url) {
            var img = document.createElement('img');
            img.src = message.attachment_url;
            img.alt = message.attachment_original_name || 'image';
            img.className = 'chat-image-attachment';
            msgContent.appendChild(img);
        }

        if (message.body) {
            var p = document.createElement('p');
            p.className = 'mb-0';
            p.textContent = message.body;
            msgContent.appendChild(p);
        }

        var time = document.createElement('small');
        time.className = 'text-muted d-block mt-1';
        time.textContent = message.time;

        content.appendChild(msgContent);
        content.appendChild(time);
        chats.appendChild(content);

        if (messagesEl) {
            messagesEl.appendChild(chats);
        }
    }

    function appendMessages(list) {
        if (!list || !list.length) {
            return;
        }

        list.forEach(function (message) {
            renderMessage(message);
            if (message.id > lastId) {
                lastId = message.id;
            }
        });

        scrollToBottom();
    }

    function updatePartnerStatus(online) {
        if (statusTextEl) {
            statusTextEl.textContent = online ? 'Online' : 'Offline';
        }
        if (statusAvatarEl) {
            statusAvatarEl.classList.toggle('avatar-online', !!online);
        }
    }

    function poll() {
        if (!pollUrl) {
            return;
        }

        fetch(pollUrl + '?after=' + lastId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then(function (response) {
                return response.ok ? response.json() : null;
            })
            .then(function (data) {
                if (!data) {
                    return;
                }
                appendMessages(data.messages);
                updatePartnerStatus(data.partner_online);
            })
            .catch(function () {
                // Silently ignore a missed poll - the next tick will retry.
            });
    }

    if (pollUrl) {
        pollTimer = window.setInterval(poll, 4000);
        window.addEventListener('beforeunload', function () {
            if (pollTimer) {
                window.clearInterval(pollTimer);
            }
        });
    }

    var form = document.getElementById('chat-form');
    var imageInput = document.getElementById('chat-image-input');
    var imagePreview = document.getElementById('chat-image-preview');
    var imageRemoveBtn = document.getElementById('chat-image-remove');
    var textInput = form ? form.querySelector('input[name="body"]') : null;
    var sending = false;

    if (imageInput) {
        imageInput.addEventListener('change', function () {
            var file = imageInput.files[0];
            if (!file || !imagePreview) {
                return;
            }
            var reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.querySelector('img').src = e.target.result;
                imagePreview.hidden = false;
            };
            reader.readAsDataURL(file);
        });
    }

    if (imageRemoveBtn) {
        imageRemoveBtn.addEventListener('click', function () {
            if (imageInput) {
                imageInput.value = '';
            }
            if (imagePreview) {
                imagePreview.hidden = true;
                imagePreview.querySelector('img').src = '';
            }
        });
    }

    if (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            if (sending || !storeUrl) {
                return;
            }

            var hasBody = textInput && textInput.value.trim().length > 0;
            var hasImage = imageInput && imageInput.files.length > 0;

            if (!hasBody && !hasImage) {
                return;
            }

            sending = true;

            var formData = new FormData(form);

            fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            })
                .then(function (response) {
                    return response.ok ? response.json() : Promise.reject(response);
                })
                .then(function (data) {
                    if (data && data.message) {
                        appendMessages([data.message]);
                    }
                    form.reset();
                    if (imagePreview) {
                        imagePreview.hidden = true;
                        imagePreview.querySelector('img').src = '';
                    }
                })
                .catch(function () {
                    // Leave the composer filled in so the user can retry sending.
                })
                .finally(function () {
                    sending = false;
                });
        });
    }

    scrollToBottom();
})();

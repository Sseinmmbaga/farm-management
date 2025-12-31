{{-- Notification Dropdown Component --}}
<div class="dropdown" id="notificationDropdown">
    <a href="#" class="topbar-icon position-relative" data-bs-toggle="dropdown" aria-expanded="false" id="notificationBell">
        <i class="fas fa-bell"></i>
        <span class="badge bg-danger rounded-pill notification-badge" id="notificationBadge" style="display: none;">0</span>
    </a>
    <div class="dropdown-menu dropdown-menu-end notification-dropdown shadow-lg" style="width: 360px; max-height: 450px; overflow-y: auto;">
        <div class="dropdown-header d-flex justify-content-between align-items-center border-bottom pb-2">
            <h6 class="mb-0">Notifications</h6>
            <a href="{{ route('notifications.index') }}" class="text-primary small">View All</a>
        </div>

        <div id="notificationList" class="notification-list">
            <div class="text-center py-4">
                <i class="fas fa-spinner fa-spin text-muted"></i>
                <p class="text-muted small mb-0">Loading...</p>
            </div>
        </div>

        <div class="dropdown-footer border-top pt-2 text-center">
            <a href="{{ route('notifications.index') }}" class="text-primary small">
                See all notifications <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</div>

<style>
.notification-dropdown {
    padding: 0;
}

.notification-dropdown .dropdown-header {
    background-color: #f8f9fa;
    padding: 12px 15px;
}

.notification-dropdown .dropdown-footer {
    background-color: #f8f9fa;
    padding: 10px 15px;
}

.notification-item {
    padding: 12px 15px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background-color 0.2s;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: #e8f4fd;
}

.notification-item .notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
}

.notification-item .notification-content {
    flex: 1;
    margin-left: 12px;
}

.notification-item .notification-title {
    font-weight: 600;
    font-size: 0.875rem;
    margin-bottom: 2px;
}

.notification-item .notification-message {
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 2px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.notification-item .notification-time {
    font-size: 0.75rem;
    color: #adb5bd;
}

.notification-empty {
    text-align: center;
    padding: 30px 15px;
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -8px;
    font-size: 0.65rem;
    min-width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationBell = document.getElementById('notificationBell');
    const notificationBadge = document.getElementById('notificationBadge');
    const notificationList = document.getElementById('notificationList');

    // Fetch notifications when dropdown is opened
    notificationBell.addEventListener('click', function() {
        fetchNotifications();
    });

    // Initial fetch
    fetchNotifications();

    // Poll for new notifications every 60 seconds
    setInterval(fetchUnreadCount, 60000);

    function fetchNotifications() {
        fetch('{{ route("notifications.dropdown") }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            updateBadge(data.unread_count);
            renderNotifications(data.notifications);
        })
        .catch(error => {
            console.error('Error fetching notifications:', error);
        });
    }

    function fetchUnreadCount() {
        fetch('{{ route("notifications.unread-count") }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            updateBadge(data.unread_count);
        })
        .catch(error => {
            console.error('Error fetching unread count:', error);
        });
    }

    function updateBadge(count) {
        if (count > 0) {
            notificationBadge.textContent = count > 99 ? '99+' : count;
            notificationBadge.style.display = 'flex';
        } else {
            notificationBadge.style.display = 'none';
        }
    }

    function renderNotifications(notifications) {
        if (notifications.length === 0) {
            notificationList.innerHTML = `
                <div class="notification-empty">
                    <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0">No new notifications</p>
                </div>
            `;
            return;
        }

        let html = '';
        notifications.forEach(notification => {
            html += `
                <div class="notification-item unread d-flex" data-id="${notification.id}" onclick="window.location.href='${notification.action_url || '{{ route("notifications.index") }}'}'">
                    <div class="notification-icon bg-${notification.color} text-white">
                        <i class="fas ${notification.icon}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${notification.title}</div>
                        <div class="notification-message">${notification.message}</div>
                        <div class="notification-time"><i class="fas fa-clock me-1"></i>${notification.created_at}</div>
                    </div>
                </div>
            `;
        });

        notificationList.innerHTML = html;
    }
});
</script>
@endpush

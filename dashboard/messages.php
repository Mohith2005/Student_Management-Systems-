<?php
session_start();
require_once "../config.php";
require_once "../components/navigation_links.php";
require_once "../components/navbar.php";

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../index.html");
    exit;
}

$student_id = $_SESSION['student_id'];

// Get all faculty members
$sql = "SELECT id, name, department FROM faculty";
$faculty_result = mysqli_query($conn, $sql);
$faculty_members = [];
while ($faculty = mysqli_fetch_assoc($faculty_result)) {
    $faculty_members[] = $faculty;
}

// Get conversation history if a faculty is selected
$selected_faculty = isset($_GET['faculty_id']) ? (int)$_GET['faculty_id'] : null;
$messages = [];

if ($selected_faculty) {
    $sql = "SELECT m.*, f.name as sender_name 
            FROM messages m 
            LEFT JOIN faculty f ON m.sender_id = f.id 
            WHERE (m.sender_id = ? AND m.receiver_id = ? AND m.sender_type = 'faculty') 
            OR (m.sender_id = ? AND m.receiver_id = ? AND m.sender_type = 'student')
            ORDER BY m.sent_time ASC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiii", $selected_faculty, $student_id, $student_id, $selected_faculty);
    mysqli_stmt_execute($stmt);
    $messages_result = mysqli_stmt_get_result($stmt);
    while ($message = mysqli_fetch_assoc($messages_result)) {
        $messages[] = $message;
    }

    // Mark messages as read
    $sql = "UPDATE messages SET read_status = 1 
            WHERE sender_id = ? AND receiver_id = ? 
            AND receiver_type = 'student' AND read_status = 0";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $selected_faculty, $student_id);
    mysqli_stmt_execute($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Student Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        :root {
            --primary-color: #2196F3;
            --secondary-color: #1976D2;
            --success-color: #4CAF50;
            --danger-color: #f44336;
            --warning-color: #ff9800;
            --text-color: #333;
            --bg-color: #f4f6f9;
            --card-bg: #ffffff;
        }

        body.dark-theme {
            --text-color: #ffffff;
            --bg-color: #1a1a1a;
            --card-bg: #2d2d2d;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding-top: 60px;
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        .messages-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 20px;
            height: calc(100vh - 100px);
        }

        .faculty-list {
            background: var(--card-bg);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow-y: auto;
        }

        .faculty-item {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .faculty-item:hover,
        .faculty-item.active {
            background-color: rgba(33, 150, 243, 0.1);
        }

        .faculty-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .chat-area {
            background: var(--card-bg);
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-header {
            padding: 20px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .messages-list {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .message-bubble {
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 15px;
            margin: 5px 0;
        }

        .message-sent {
            background-color: var(--primary-color);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 5px;
        }

        .message-received {
            background-color: rgba(0,0,0,0.1);
            align-self: flex-start;
            border-bottom-left-radius: 5px;
        }

        .message-time {
            font-size: 0.8rem;
            opacity: 0.7;
            margin-top: 5px;
        }

        .message-input-area {
            padding: 20px;
            border-top: 1px solid rgba(0,0,0,0.1);
            display: flex;
            gap: 10px;
        }

        .message-input {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 20px;
            outline: none;
            resize: none;
        }

        .send-button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease;
        }

        .send-button:hover {
            background-color: var(--secondary-color);
        }

        .no-chat-selected {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #666;
        }

        .no-chat-selected i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <?php echo getNavbar('student'); ?>
    
    <div class="messages-container">
        <div class="faculty-list">
            <h2>Faculty Members</h2>
            <?php foreach ($faculty_members as $faculty): ?>
            <div class="faculty-item <?php echo $selected_faculty === $faculty['id'] ? 'active' : ''; ?>" 
                 onclick="window.location.href='?faculty_id=<?php echo $faculty['id']; ?>'">
                <div class="faculty-avatar">
                    <?php echo strtoupper(substr($faculty['name'], 0, 1)); ?>
                </div>
                <div>
                    <div><?php echo htmlspecialchars($faculty['name']); ?></div>
                    <small><?php echo htmlspecialchars($faculty['department']); ?></small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="chat-area">
            <?php if ($selected_faculty): ?>
                <?php 
                $faculty_name = '';
                foreach ($faculty_members as $faculty) {
                    if ($faculty['id'] === $selected_faculty) {
                        $faculty_name = $faculty['name'];
                        break;
                    }
                }
                ?>
                <div class="chat-header">
                    <h2>Chat with <?php echo htmlspecialchars($faculty_name); ?></h2>
                </div>
                <div class="messages-list" id="messagesList">
                    <?php foreach ($messages as $message): ?>
                        <?php 
                        $is_sent = $message['sender_type'] === 'student';
                        $bubble_class = $is_sent ? 'message-sent' : 'message-received';
                        ?>
                        <div class="message-bubble <?php echo $bubble_class; ?>">
                            <div class="message-content">
                                <?php echo htmlspecialchars($message['content']); ?>
                            </div>
                            <div class="message-time">
                                <?php echo date('M d, g:i A', strtotime($message['sent_time'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="message-input-area">
                    <textarea class="message-input" placeholder="Type your message..." id="messageInput"></textarea>
                    <button class="send-button" onclick="sendMessage()">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            <?php else: ?>
                <div class="no-chat-selected">
                    <i class="fas fa-comments"></i>
                    <h2>Select a faculty member to start chatting</h2>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Scroll to bottom of messages
        function scrollToBottom() {
            const messagesList = document.getElementById('messagesList');
            if (messagesList) {
                messagesList.scrollTop = messagesList.scrollHeight;
            }
        }

        // Call on page load
        scrollToBottom();

        // Send message function
        function sendMessage() {
            const input = document.getElementById('messageInput');
            const content = input.value.trim();
            
            if (!content) return;

            // Send message via AJAX
            $.ajax({
                url: 'send_message.php',
                method: 'POST',
                data: {
                    receiver_id: <?php echo $selected_faculty ?? 0; ?>,
                    content: content,
                    receiver_type: 'faculty'
                },
                success: function(response) {
                    if (response.success) {
                        // Add message to chat
                        const messagesList = document.getElementById('messagesList');
                        const messageDiv = document.createElement('div');
                        messageDiv.className = 'message-bubble message-sent';
                        messageDiv.innerHTML = `
                            <div class="message-content">${content}</div>
                            <div class="message-time">Just now</div>
                        `;
                        messagesList.appendChild(messageDiv);
                        
                        // Clear input and scroll to bottom
                        input.value = '';
                        scrollToBottom();
                    }
                }
            });
        }

        // Handle Enter key
        document.getElementById('messageInput')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Update theme based on system preference
        function updateTheme() {
            const isDarkMode = localStorage.getItem('theme') === 'dark';
            document.body.classList.toggle('dark-theme', isDarkMode);
        }

        // Update theme on load and changes
        document.addEventListener('DOMContentLoaded', updateTheme);
        window.addEventListener('storage', (e) => {
            if (e.key === 'theme') updateTheme();
        });
    </script>
</body>
</html>

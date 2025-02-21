<?php
session_start();
require_once "../config.php";
require_once "../components/navigation_links.php";
require_once "../components/navbar.php";

// Check if faculty is logged in
if (!isset($_SESSION['faculty_id'])) {
    header("Location: ../index.html");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Faculty Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding-top: 60px;
            background-color: #f4f6f9;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .messages-container {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .contacts-list {
            border-right: 1px solid #ddd;
            padding: 20px;
            height: calc(100vh - 120px);
            overflow-y: auto;
        }
        .message-area {
            padding: 20px;
            display: flex;
            flex-direction: column;
            height: calc(100vh - 120px);
        }
        .message-history {
            flex-grow: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .message-input {
            display: flex;
            gap: 10px;
        }
        .message-input textarea {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: none;
        }
        .btn {
            background-color: #2196F3;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #1976D2;
        }
    </style>
</head>
<body>
    <?php echo getNavbar('faculty'); ?>
    
    <div class="container">
        <h1>Messages</h1>
        
        <div class="messages-container">
            <div class="contacts-list">
                <h2>Contacts</h2>
                <!-- Contacts will be loaded here dynamically -->
            </div>
            
            <div class="message-area">
                <div class="message-history">
                    <!-- Messages will be loaded here dynamically -->
                </div>
                
                <div class="message-input">
                    <textarea placeholder="Type your message..."></textarea>
                    <button class="btn">Send</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add your JavaScript for handling messages here
    </script>
</body>
</html>

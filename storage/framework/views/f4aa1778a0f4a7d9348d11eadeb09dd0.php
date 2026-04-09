<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Update</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5; }
        .container { background-color: #ffffff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { border-bottom: 2px solid #111827; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { color: #111827; font-size: 20px; margin: 0; }
        .header.closed { border-bottom-color: #16a34a; }
        .header.closed h1 { color: #16a34a; }
        .ticket-info { background-color: #f8f9fa; border-radius: 6px; padding: 15px; margin-bottom: 20px; }
        .ticket-info p { margin: 5px 0; }
        .ticket-info strong { color: #111827; }
        .message-box { background-color: #e8f4fd; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0; border-radius: 0 6px 6px 0; }
        .message-box.closed { background-color: #f0fdf4; border-left-color: #16a34a; }
        .message-box p { margin: 0; }
        .closed-banner { background-color: #16a34a; color: #ffffff; text-align: center; padding: 14px; border-radius: 6px; font-size: 16px; font-weight: bold; margin-bottom: 20px; }
        .reply-btn { display: inline-block; background-color: #111827; color: #ffffff !important; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600; margin-top: 20px; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280; text-align: center; }
        .sender-info { font-size: 14px; color: #6b7280; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">

        <?php if($ticket->status === 'Closed' && $log->action === 'status_changed'): ?>
        
        <div class="header closed">
            <h1>✅ Ticket Closed: <?php echo e($ticket->ticket_id); ?></h1>
        </div>

        <div class="closed-banner">
            🎉 Your ticket has been resolved and closed.
        </div>

        <div class="ticket-info">
            <p><strong>Ticket ID:</strong> <?php echo e($ticket->ticket_id); ?></p>
            <p><strong>Subject:</strong> <?php echo e($ticket->title); ?></p>
            <p><strong>Status:</strong> ✅ Closed</p>
            <p><strong>Priority:</strong> <?php echo e($ticket->priority); ?></p>
        </div>

        <div class="sender-info">
            <strong>Closed by:</strong> <?php echo e($log->user->name ?? 'Support Team'); ?><br>
            <strong>Date:</strong> <?php echo e($log->created_at->format('d M Y, h:i A')); ?>

        </div>

        <div class="message-box closed">
            <p><?php echo e($log->message); ?></p>
        </div>

        <p>If you have additional concerns, you can view or re-open your ticket by clicking the link below:</p>
        <a href="<?php echo e($replyLink); ?>" class="reply-btn">View Ticket</a>

        <?php else: ?>
        
        <div class="header">
            <h1>🎫 Ticket Update: <?php echo e($ticket->ticket_id); ?></h1>
        </div>

        <div class="ticket-info">
            <p><strong>Ticket ID:</strong> <?php echo e($ticket->ticket_id); ?></p>
            <p><strong>Subject:</strong> <?php echo e($ticket->title); ?></p>
            <p><strong>Status:</strong> <?php echo e($ticket->status); ?></p>
            <p><strong>Priority:</strong> <?php echo e($ticket->priority); ?></p>
        </div>

        <div class="sender-info">
            <strong>From:</strong> <?php echo e($log->user->email ?? 'Support Team'); ?><br>
            <strong>Date:</strong> <?php echo e($log->created_at->format('d M Y, h:i A')); ?>

        </div>

        <div class="message-box">
            <p><?php echo e($log->message); ?></p>
        </div>

        <p>You can reply to this ticket by clicking the button below:</p>
        <a href="<?php echo e($replyLink); ?>" class="reply-btn">Reply to Ticket</a>
        <?php endif; ?>

        <div class="footer">
            <p>This is an automated message from MICROLAB Support System.</p>
            <p>Please do not reply directly to this email.</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\laravel\the_pulsepals\resources\views/emails/ticket-activity.blade.php ENDPATH**/ ?>
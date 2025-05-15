CREATE TABLE attendance_system_users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR (255),
    password TEXT,
    first_name VARCHAR (255),
    last_name VARCHAR (255),
    is_admin TINYINT,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE attendance_records (
    attendance_record_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    date_added DATE,
    time_in TIME NULL,
    time_out TIME NULL,
    timestamp_record_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE leaves (
    leave_id INT AUTO_INCREMENT PRIMARY KEY,
    description TEXT,
    user_id INT,
    date_start DATE, 
    date_end DATE,
    status VARCHAR(255) DEFAULT "Pending",
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP  
)

CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT, -- recipient
    message TEXT,
    is_read BOOL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

# attendance-monitoring-QR-
download node module packages and vendor
extract the project and paste it into xampp/htdocs folder
create a new data base with the name of the project data base
import the qr_attendance_db in it
also go to attendance table and add this query:
ALTER TABLE attendance
ADD COLUMN time_out DATETIME;
Next create new table "tbl_users" and put this query:
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);
username:hayath
password:AIMCA
you can change it by manage users tab

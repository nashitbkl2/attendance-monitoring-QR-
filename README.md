# attendance-monitoring-QR-
1.download node module packages and vendor

2.extract the project and paste it into xampp/htdocs folder

3.create a new data base with the name of the project data base

4.import the qr_attendance_db in it

also go to attendance table and add this query:

ALTER TABLE attendance
ADD COLUMN time_out DATETIME;

5.Next create new table "tbl_users" and put this query:

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);

6.username:hayath

password:AIMCA

7.you can change it by manage users tab

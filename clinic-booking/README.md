# CareClinic - Clinic Appointment Booking System (PHP + MySQL)

## Setup (XAMPP)
1. XAMPP install karo, Apache aur MySQL start karo.
2. Ye poora `clinic-booking` folder `C:\xampp\htdocs\` me paste karo.
3. Browser me `http://localhost/phpmyadmin` kholo -> Import -> `database.sql` select karo -> Go.
4. Browser me `http://localhost/clinic-booking` kholo.

## Logins
- Admin: admin@clinic.com / password   (pehle login pe password badal lo)
- Doctor: Admin panel se add karo
- Patient: Register page se

## Testing flow
1. Admin login -> Doctors -> ek doctor add karo
2. Doctor login -> My Schedule -> din aur time save karo
3. Patient register -> Book -> doctor, date, slot chuno
4. Doctor login -> Approve -> Completed + prescription
5. Same slot dobara book karke dekho (double booking block hoga)

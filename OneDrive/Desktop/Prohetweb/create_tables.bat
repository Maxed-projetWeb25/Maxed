@echo off
echo Creating database and tables...
mysql -u root < database.sql
if %errorlevel% equ 0 (
    echo Database and tables created successfully!
) else (
    echo Error creating database and tables. Please check your MySQL installation.
)
pause 
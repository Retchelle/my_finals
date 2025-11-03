QuickRent: A Web-Based Rental Property Management System
Project Title and Short Description
QuickRent is a web-based application developed in PHP and MySQL that allows admins (landlords) to efficiently manage properties, tenants, rent, and maintenance requests. Tenants can log in to view payment history and submit maintenance requests. The system centralizes data management to make property administration faster, organized, and paperless.

Features Implemented
Admin Features:
•	Admin login and authentication
•	Add, edit, delete, and view tenants
•	Manage property details (address, rent amount, status)
•	Record and view rent and payments
•	Manage and update maintenance requests
•	Dashboard view with charts (rent collection, property status, maintenance summary)
Tenant Features:
•	Tenant login and profile access
•	View payment history and due dates
•	Submit maintenance requests
•	View maintenance request status

Setup Instructions (How to Run Locally)
1. Open Laragon
2. Open phpMyAdmin and create a new database named 'rental_property_management'.
3. Import the SQL file “rental_property_management(3).sql”.
4. Place the project folder inside “C:\laragon\www\” .
5. Open your browser and go to “http://localhost/rental_property_management/”.
6. Login using default admin account (ex. username: admin / password: admin123).

Database Structure
Database Name: rental_property_management
Table Name	Columns
admin	admin_id (PK), username, password
tenant	tenant_id (PK), fname, mname, lname, contact_number, username, email, password
property	property_id (PK), address, rent_amount, tenant_id (FK), property
rent	rent_id (PK), tenant_id (FK), property_id (FK), start_date, end_date, rent_amount, status
payment	payment_id (PK), tenant_id (FK), property_id (FK), amount, payment_date, due_date, status
maintenance_request	request_id (PK), tenant_id (FK), property_id (FK), request_date, description, status


List of PHP Functions and Their Purposes
Function Name	Purpose
connectDatabase()	Establishes connection to the MySQL database
addTenant	Inserts new tenant information
updateTenant	Updates tenant record
deleteTenant	Deletes tenant by 
addProperty	Adds a new property
updateProperty	Edits existing property details
deleteProperty	Removes property record
getProperties	Fetches property list
addPayment	Records new payment
getPayments	Fetches all payment records
addMaintenanceRequest	Submits maintenance request from tenant
updateMaintenanceStatus	Updates maintenance request status
getMaintenanceRequests	Displays list of maintenance requests
loginUser($username, $password)	Handles user authentication
logout	Logs out the current session
getDashboardData	Retrieves summary data for dashboard charts

List of API Endpoints and Their Descriptions
HTTP Method	Endpoint	Description
POST	/api/login.php	Authenticates admin or tenant user
GET	/api/tenants/view.php	Retrieves tenant list
POST	/api/tenants/add.php	Adds a new tenant
POST	/api/tenants/edit.php	Updates tenant record
GET	/api/tenants/delete.php	Deletes tenant
GET	/api/properties/view.php	Retrieves property list
POST	/api/properties/add.php	Adds a new property
POST	/api/properties/edit.php	Updates property record
GET	/api/properties/delete.php	Deletes property
GET	/api/payments/view.php	Retrieves all payments
POST	/api/payments/add.php	Records new payment
GET	/api/maintenance/view.php	Lists maintenance requests
POST	/api/maintenance/add.php	Submits new maintenance request
POST	/api/maintenance/update.php?	Updates maintenance status




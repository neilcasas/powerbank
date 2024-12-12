CREATE DATABASE powerbank;
USE powerbank;

CREATE TABLE client (
	client_id INT(5) NOT NULL AUTO_INCREMENT,
    client_name VARCHAR(50) NOT NULL,
    address VARCHAR(50) NOT NULL,
    phone_number CHAR(11) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    date_of_birth DATE NOT NULL,
    PRIMARY KEY (client_id)
);

CREATE TABLE account (
	acct_id INT(5) NOT NULL AUTO_INCREMENT,
    client_id INT(5) NOT NULL,
    acct_type ENUM('SAVINGS', 'CHECKING') NOT NULL,
    acct_level ENUM('REGULAR', 'PREMIUM', 'VIP') NOT NULL,
    acct_balance DECIMAL(9,2) NOT NULL,
    PRIMARY KEY (acct_id),
    FOREIGN KEY (client_id) REFERENCES client(client_id)
);

CREATE TABLE savings_account (
	acct_id INT(5) NOT NULL,
    savings_interest_rate DECIMAL(4,3) NOT NULL,
    PRIMARY KEY(acct_id),
    FOREIGN KEY (acct_id) REFERENCES account(acct_id) ON DELETE CASCADE
);

CREATE TABLE checking_account (
	acct_id INT(5) NOT NULL,
    overdraft_limit DECIMAL(9,2) NOT NULL,
    PRIMARY KEY(acct_id),
    FOREIGN KEY (acct_id) REFERENCES account(acct_id) ON DELETE CASCADE
);

CREATE TABLE employee (
	employee_id INT(5) NOT NULL AUTO_INCREMENT,
    employee_name VARCHAR(50) NOT NULL,
    employee_position VARCHAR(50) NOT NULL, 
    employee_email VARCHAR(50) NOT NULL UNIQUE,
    date_of_birth DATE NOT NULL,
    salary DECIMAL(9,2) NOT NULL,
    PRIMARY KEY (employee_id)
);

CREATE TABLE loan (
	loan_id INT(5) NOT NULL AUTO_INCREMENT,
    client_id INT(5) NOT NULL,
    loan_type ENUM('BUSINESS', 'CAR', 'HOUSING') NOT NULL,
    loan_amount DECIMAL(9,2) NOT NULL,
    loan_interest_rate DECIMAL(4,3) NOT NULL,
    loan_start_date DATE NOT NULL,
	loan_end_date DATE NOT NULL,
    employee_id INT(5) NOT NULL,
    PRIMARY KEY (loan_id),
    FOREIGN KEY (client_id) REFERENCES client(client_id),
    FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
);

CREATE TABLE request (
    request_id INT(5) NOT NULL AUTO_INCREMENT,
    client_id INT(5) NOT NULL,
    request_type ENUM('LOAN_CREATE','ACCOUNT_CREATE', 'ACCOUNT_DELETE') NOT NULL,
    request_date DATE NOT NULL,
    PRIMARY KEY (request_id),
    FOREIGN KEY (client_id) REFERENCES client(client_id)
);

CREATE TABLE account_request (
    request_id INT(5) NOT NULL,
    acct_id INT(5), -- Nullable if it's a new account
    acct_type ENUM('SAVINGS', 'CHECKING') NOT NULL,
    acct_level ENUM('REGULAR', 'PREMIUM', 'VIP') NOT NULL,
    acct_request_type ENUM('ACCOUNT_CREATE', 'ACCOUNT_DELETE') NOT NULL,
    PRIMARY KEY (request_id),
    FOREIGN KEY (request_id) REFERENCES request(request_id) ON DELETE CASCADE
);

CREATE TABLE loan_request (
    request_id INT(5) NOT NULL,
    loan_type ENUM('BUSINESS', 'CAR', 'HOUSING') NOT NULL,
    loan_amount DECIMAL(9,2) NOT NULL,
    PRIMARY KEY (request_id),
    FOREIGN KEY (request_id) REFERENCES request(request_id) ON DELETE CASCADE
);

INSERT INTO client VALUES
    (1, "Jacob Lash", "43rd. St.", "09358681544", "jacoblash@email.com", "1988-03-11"),
    (2, "Dean Abrams", "44th. St.", "09541245567", "abrdean@email.com", "1968-11-03"),
    (3, "Elizabeth McGinnis", "51st. St.", "09614567898", "lizmcginnis@email.com", "2001-07-14"),
    (4, "Mark Bebop", "52nd. St.", "09789651211", "markbebop@email.com", "1996-07-04");

INSERT INTO account VALUES
    (1, 1, "SAVINGS", "VIP", 481536.75),
    (2, 1, "CHECKING", "REGULAR", 12000.00),
    (3, 2, "SAVINGS", "REGULAR", 515978.25),
    (4, 3, "CHECKING", "PREMIUM", 128000.50),
    (5, 4, "SAVINGS", "PREMIUM", 80050.75),
    (6, 4, "CHECKING", "VIP", 150000.00);

INSERT INTO savings_account VALUES
    (1, 0.050),
    (3, 0.025),
    (5, 0.030);

INSERT INTO checking_account VALUES
    (2, 12000.00),
    (4, 15000.00),
    (6, 20000.00);

INSERT INTO employee VALUES
    (1, "Isagi Yoichi", "Junior Loan Officer", "isagi@powerbank.com", "1990-06-15", 215000.00),
    (2, "Seishiro Nagi", "Senior Loan Officer", "nagi@powerbank.com", "1991-03-20", 350000.00),
    (3, "Rin Itoshi", "Senior Loan Officer", "rin@powerbank.com", "1989-12-01", 350000.00),
    (4, "Ego Jinpachi", "Bank Manager", "ego@powerbank.com", "1975-08-25", 500000.00),
    (5, "Kylian Mbappe", "Head of Bank Operations", "mbappe@powerbank.com", "1985-05-20", 1250000.00),
    (6, "Julian Loki", "Head of Marketing", "loki@powerbank.com", "1983-11-10", 1000000.00),
    (7, "John Paurbanc", "Chief Executive Officer", "paurbanc@powerbank.com", "1970-01-15", 4000000.00),
    (8, "Elliot Alderson", "IT Admin", "elliot@powerbank.com", "1993-09-17", 250000.00);

INSERT INTO loan VALUES
    (1, 1, "BUSINESS", 1000000.00, 0.100, "2004-12-14", "2009-12-14", 1),
    (2, 1, "CAR", 800000.00, 0.050, "2005-11-05", "2010-11-05", 2),
    (3, 2, "BUSINESS", 1500000.00, 0.100, "2008-07-21", "2013-07-21", 3),
    (4, 2, "HOUSING", 2000000.00, 0.150, "2008-04-20", "2023-04-20", 4),
    (5, 3, "BUSINESS", 300000.00, 0.100, "2011-03-05", "2016-03-05", 5),
    (6, 4, "CAR", 600000.00, 0.050, "2003-09-30", "2008-09-30", 6);

CREATE TABLE credentials (
    username VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL,
    client_id INT(5), -- Nullable if it's a client
    employee_id INT(5), -- Nullable if it's an employee
    role ENUM('CLIENT', 'EMPLOYEE', 'MANAGER', 'EXECUTIVE', 'ADMIN') NOT NULL,
    PRIMARY KEY (username),
    FOREIGN KEY (client_id) REFERENCES client(client_id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employee(employee_id) ON DELETE CASCADE
);
-- Employees with credentials
INSERT INTO credentials (username, password, client_id, employee_id, role) VALUES
    ("isagi@powerbank.com", "isagi", NULL, 1, "EMPLOYEE"),
    ("nagi@powerbank.com", "nagi", NULL, 2, "EMPLOYEE"),
    ("rin@powerbank.com", "rin", NULL, 3, "EMPLOYEE"),
    ("ego@powerbank.com", "ego", NULL, 4, "MANAGER"),
    ("mbappe@powerbank.com", "mbappe", NULL, 5, "EXECUTIVE"),
    ("loki@powerbank.com", "loki", NULL, 6, "EXECUTIVE"),
    ("paurbanc@powerbank.com", "paurbanc", NULL, 7, "EXECUTIVE"),
    ("elliot@powerbank.com", "elliot", NULL, 8, "ADMIN");

-- Existing clients with roles as 'client'
INSERT INTO credentials (username, password, client_id, employee_id, role) VALUES
    ("jacoblash@email.com", "jacoblash", 1, NULL, "CLIENT"),
    ("abrdean@email.com", "abrdean", 2, NULL, "CLIENT"), 
    ("lizmcginnis@email.com", "lizmcginnis", 3, NULL, "CLIENT"), 
    ("markbebop@email.com", "markbebop", 4, NULL, "CLIENT");


# Aspire BE Challenge

### Running the server

The best way to run this project is to use `docker desktop`. Following are the steps -

1. Make sure docker desktop is running on your system
2. Go to the project directory and run `./vendor/bin/sail up`


You can find the **postman** collection in the root directory of the repo.

**Note**: For the sake of ease, I've allowed the consumer to change the status of a loan from _**pending**_ to _**approved**_. 

### API endpoints

#### AuthController
1. `/api/login/`
2. `/api/register/`
3. `/api/logout/`

#### LoanController
1. `/api/create-loan-request/`
2. `/api/approve-loan/`
3. `/api/get-loans/`
4. `/api/add-loan-repayment/`

Pricing exercise
===========

A Symfony project created on September 1, 2018.

Price calculation service
-----

In this exercise is assumed that all products are already saved in database 
and this functionality is handled somewhere else.

Price calculator is a micro service which is a independent part of the system 
and the only task it has is to calculate asked prices. When the request comes in
it is already known which prices (net or gross) should be calculated.


REST API for price calculation service
-----

**Tax type can have two values: 1 or 2.** All other values will throw an exception.
This is because the tax class can only be **7%** or **19%** and a user should not be able to
insert any other values. 

| Tax type  | Tax percent | 
| --------- | ------------| 
| 1         | 7%          | 
| 2         | 19%         | 

**Request will only take integer values.** All other types will throw an exception.
For example:
    100 is 1 €,
    99 is 0,99 €,
    1000 is 10 €
*This is how the rounding problems can be avoided.*

In the request there should always be the following fields, otherwise an exception will be thrown:
name
price
taxType.

Methods:
-------

**Calculating net price from given gross price:**

POST /product/calculate-net 
Content-Type: application/json    
Expected result 200

Request body:
```
{
    "products": [
        {
            "name": "Milch",
            "price": 107,       // This is gross price 1.07 EUR
            "taxType": 1
        },
        {
            "name": "Sojamilch",
            "price": 119,       
            "taxType": 2
        }
    ]
}
```
Result body:
```
{
    "products": [
        {
            "name": "Milch",
            "price": 107,       // This is original price (1.07 EUR) given in request
            "taxType": 1,
            "net": 100       // This is calculated net price 1 EUR from the original price 
        },
        {
            "name": "Sojamilch",
            "price": 119,       
            "taxType": 2,
            "net": 100
        }
    ]
}
```

**Calculating gross price from given net price:**

POST /product/calculate-gross 
Content-Type: application/json    
Expected result 200
        
Request body:
```
{
    "products": [
        {
            "name": "Milch",
            "price": 100,       // This is net price
            "taxType": 1
        },
        {
            "name": "Sojamilch",
            "price": 100,       
            "taxType": 2
        }
    ] 
}
```
Result body:
```
{
    "products": [
        {
            "name": "Milch",.m
            "price": 100,       // This is original price given in request
            "taxType": 1,
            "gross": 107
        },
        {
            "name": "Sojamilch",
            "price": 100,       
            "taxType": 2,
            "gross": 119
        }
    ]
}
```

**Error cases:**

| Error code  | Error message                    | 
| ----------- | ---------------------------------| 
| 110         | Form is not valid                | 
| 111         | Price cannot be calculated       | 
| 112         | Product not found                | 
| 113         | Tax type not found               | 

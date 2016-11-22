#XRM documentation

XRM is an application developed to enable media agencies to more easily setup XPlenty ETL packages for digital media reporting accounts.

XRM is an application developed to enable media agencies to input digital media reporting accounts, including account information, commissions, and campaign labels in a web user interface. This information is stored directly in your AWS Redshift database, thereby enabling you to use this data to more easily customize your Reports.  ARM also includes some features designed to help manage your packages on XPlenty, a 3rd party data pipeline ETL service. 
XRM does not include a license to XPlenty, which must be purchased separately with XPlenty.

#Hosting Requirements

A web server with:
- Apache2
- PHP7
- PostgreSQL PHP module for communicating with Redshift

An AWS Redshift cluster

##XPlenty Requirements
A XPlenty account is required, with master packages for Adwords, Bing, Facebook, and Google Analytics created.  These master package IDs are entered into XRM.


#Installation

- Make sure you have Symfony3 installed as this is a Symfony3 app
- Setup the required credentials in src\AppBundle\Configuration\arm.yml
- Password protect your application with htpasswd

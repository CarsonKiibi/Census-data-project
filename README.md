# Canadian Census Data Project
**Carson Kyba, Katie Louie (louiekat), Linus Moreau (lhlafont)**

## Setup 
This project uses PHP 8.2.11, Composer 2.6.5, and Laravel 10.29.0. Make sure you have those installed and check with
```
php artisan about
```

To initialize the database (create tables, load data)
```
php artisan app:start
```
To run the project
```
php artisan serve
```

The original repository was created using university credentials and our university github. As a result it will likely be deleted in the future, so I made this new repo.
No edits have been made other than to the readme.

## Summary
This project is for modelling the Canadian Census of Population from Statistics Canada and other related geographic data. We have created a script to create a SQLite database reflecting our relational model as presented in our ER diagram, along with populating it with data. We have also created sample queries for interpreting the data, as well as a frontend for running the queries and displaying their results. 

The main data used is from the 2021 Census Public Use Microdata File (PUMF) and documentation. Other data included, particularly the geographic data, is drawn from other sources and this project makes no claim to their accuracy.

Note that while this project uses the Laravel framework which provides support for integrated query building, we have avoided using this feature in order to create our queries from scratch.

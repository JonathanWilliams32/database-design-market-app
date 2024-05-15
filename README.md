README.md

Project Name: SecureWebApp

SecureWebApp
Description:
- This project uses XAMPP to run an apache server for the frontend and SQL backend.
- Phase 1:
    SecureWebApp is a web application developed using PHP and MySQL to provide secure user registration, login, and authentication functionalities. The application utilizes best practices to protect against common security vulnerabilities such as SQL injection and password hashing.
- Phase 2:
    This app now features a homepage with useful information to the user such as who is logged in, a table of item listings they have posted, and buttons to navigate to newly added pages. The users can post items, look at everyone's postings and find certain items by their category tags, and leave reviews.

Features:
- Phase 1:
    - User Registration: Allows users to create a new account by providing necessary information such as username, password, first name, last name, and email. Duplicate username and email checks are implemented to prevent registration with existing credentials.
    - User Login: Registered users can securely log in to their accounts using their username and password. Passwords are hashed before storing in the database, and password verification is performed securely.
    - Session Management: User sessions are managed securely to authenticate users and provide access control to different parts of the application.
    - Protection against SQL Injection: Prepared statements are used for database queries to prevent SQL injection attacks. User input is properly sanitized and bound to parameters in SQL queries.
- Phase 2:
    - Posting Items Feature: Allows logged in users to post items to the app that contain a title, short description, some category tags, and a price. A user can only do this up to two times in a given day and will be alerted and stopped if attempting to post a third time in a day.
    - Search Interface: The collection of all items that users have posted are availible to view on the page after they have clicked on the "The Market" button on the home page. From this page users an see some of the information about each item - everything except the item description, which is viewable after the user clicks the "view details" button on an item's row. The user can input a term to search for in which they will be returned a table view of items that have a matching category tag, otherwise, just an empty table. Additionally the user can click the "Show All" button to show all of the items again.
    - Review System: A logged in user can leave reviews once they have gone to the details page of a given item. The reviews consist of a dropdown menu with excellent, good, fair, and poor as possible choices and a text section for short comments. They cannot leave reviews on items they themselves have posted. A user can only leave 1 review on an item they are able to leave a review on and they can only give up to 3 reviews in a given day. If the user tries to write reviews that go against these conditions, they will not be allowed to do so and will receive and error message telling them so.
- Phase 3:
    - Finding most expensive items for a cateory feature: An added feature to "The Market" page. The logged in user can use this new field to find the (up to) 3 most expensive items for a category they type in. Results are shown in table form the same as the rest of the functionality on this page.
    - Finding "best" items posted by a user feature: This feature allows the logged in user to find items posted by a user of their choice (by typing in a username) that has gotten at least 1 'excellent' or 'good' rating review. This feature is availible on the newly made page that can be navigated to after the user clicks the 'Best Items' button from the homepage.
    - Listing the user(s) that posted the most items on a chosen data feature: This feature is on the newly made page that can be navigated to after the user has clicked the 'Most Posts' button on the home page. The user can use the calender dropdown to select a date and press search to get a list of the user or users (in the case of a tie) that have posted the most number of items on the date.
    - Favoriting system: This new system allows a logged in user to 'favorite' another user in the app by clicking a button with a heart icon next to the username of the user they wish to favorite. Using a new 'favorites' table we can keep track of which user has favorited which other user. Additioanlly, a user cannot favorite themselves and another user they have already done so with.
    - Search Users Page: This newly made page contains a lot of new features -- part of several of the requirements for this phase. It by deafault will show a list of all the usernames with accounts created in the app. From this page the logged in user can favorite other users. Additionally it is where the functionality for the following features are:
        - Finding common favorites feature: The logged in user can use the two dropdown menues to select 2 usernames to find the users that both have favorited. 
        - Finding users with non-excellent item posts feature: This button ('Find Non-Excellent Posterse') outputs a list in the table view of all the users that have do not have any posted items that have gotten at least 3 'excellent' rating reviews.
        - Finding users who are poor reviewers feature: This button ('Find Poor Reviewers') outputs a list of all the users that have left reviews, but all of their reviews left hae ratings of 'poor'.



Credits:
This project was developed as part of the Secure Web Application Development course (COMP440) at [California State University Northridge]. Contributors to the project include:

[Group Team: #6]
[Jonathan Williams]
    - Phase 1:
        - 50/50 on designing/creating the database.
        - Implemented login page.
        - Implemented registration page.
        - Implemented logout functionality.
    Phase 2:
        - 50/50 on designing creating new tables in the database for this phase
        - Implemented form / interface to allow users to post or list an item with several featured info
        - Helped with design of all interfaces
        - Implemented review system and logic to handle project required conditions
        - Populated app with user inputed data
    Phase 3:
        - 50/50 on designing creating new table in the database for this phase
        - Create new pages/UI for added features
        - Implemented feature to list users with the most posted items on a selected date
        - Implemented (in progress) the feature to get the common favorite users from 2 selected users from dropdowns
        - Implemented feature to list users who hae never posted any items that have gotten at least 3 'excellent' reviews on a single item
        - Implement feature to display list of users that have only left reviews with a rating of 'poor'

[Justin Lee]
    - Phase 1:
        - Implemented the landing page.
        - Implemented the index (home) page.
        - 50/50 on designing/creating the database.
        - Research and implementation of protection against sql injection.
    - Phase 2:
        - 50/50 on designing creating new tables in the database for this phase
        - Implemented the search interface that searches all the items and finds items that have the user requested category tags
            - Implemented outputting the table of the search results
        - Protection against SQL injection
    Phase 3:
        - 50/50 on designing creating new table in the database for this phase
        - Implementing getting most expensive items in each category
        - Implementing feature to list a chosen user's items that have gotten 'good' or 'excellent' reviews

[Additioanl Resources Used]
    - Bootstrap web page styling: https://www.bootstrapcdn.com/ 

[Demo Video Link]
    - YouTube videos are categorized as "unlisted" so the following links will be required to find the videos
    - Phase #1 Demo: https://youtu.be/QYD9jwnVkbQ
    - Phase #2 Demo: https://youtu.be/B5hvHOLGGiI 
    - Phase #3 Demo: https://youtu.be/PU6OHqZ951o 
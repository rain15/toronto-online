
As part of a course, I built this WordPress website from a Photoshop design file. It was created from scratch.

Demo site is at http://toronto.hemapillay.com/

## Challenges:

While migrating the site from local install to domain, the images were not displaying. What finally worked was the solution provided  to rebuilt the image meta data in the database and then run force regenerate thumbnails plugin.

Before finding this solution, I contacted the domain support whose solution was to manually go in the WordPress admin dashboard and upload all the images again and connect them to all the pages and posts that had featured images. Even though this site does not have many pages where this solution would have worked, I wanted to figure out if this could be solved through some sort of automation and eventually after few hours of online searching, I found the solution that worked as mentioned here.

In the next site that I built which is Around the World (ATW) travel agency, this solution did not work. I went in manually into the database and using SQL, updated the data for all the images to reflect the right path instead of localhost and then ran the force regenerate thumbnails plugin.

## Skills:

1. WordPress Custom Queries
2. Custom Post Types
3. Advanced Custom Fields (ACF) WordPress plugin
4. Regenerate Thumbnails plugin
5. Contact Form 7 plugin
6. Event Importer for Meetup and The Events Calendar plugin
7. Image Widget plugin by Modern Tribe to add the image widgets in middle of front page
8. Custom Page Templates
9. Integration of bxSlider javascript plugin to create sliders on home page
10. Creation of dynamic page title to improve SEO
11. Photoshop – extracting image, measurements, fonts, colors

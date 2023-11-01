# Explanation

> Please bear in mind that WordPress is completely new to me, and that I attempted to produce a self-contained Wordpress plugin only.

## The problem to be solved in your own words.
An administrator needs the functionality to trigger an immediate refresh of a sitemap of internal links on a site after the plugin is installed, and that the plugin should perform regular (hourly) updates itself after that.

## A technical specification of your design, explaining how it works.
The plugin architecture is extensible by design, both because it's good coding practice, but also because it was explicitly requested in the assessment specification.

I the nature of custom code, such as plugins, means increased risk of naming collisions, and so all the files are namespaced.

I soon realised that class-based plugins make namespacing easier to implement.

### Structure of plugin
- The plugin is based on the ideas of ***The WordPress Plugin Boilerplate ([wppb.io](wppb.io))***  to aid extensibility.
- I got a good understanding of it from this [wplauncher.com blog post](https://blog.wplauncher.com/wordpress-plugin-development-for-beginners/).

The plugin creates a database table for its own use during activation, and provides an admin interface accessible from menu: (***Settings > Internal Crawler***)

The ***Crawl Now*** button triggers an ajax request to retrieve the internal links for the URL, which are then stored in the database along with timestamps, and returned to the same page (in the `<textarea>`).

## The technical decisions you made and why.
Although I was completely unfamiliar with Wordpress, I decided to build the Wordpress plugin rather than a PHP app since WP Media uses WordPress, and I'd need to get familiar with it anyway.

I spent a lot of time understanding various parts of WordPress and how to design an extensible plugin architecture.

My overall idea was to
1. Understand enough WordPress to create a plugin.
2. Create a good structure for the plugin.
3. Keep things simple.

There are parts of the assessment specification that I have not yet completed.  I hope to get to more of these soon, and I will update this repository accordingly.

I hope that the work I've done give some indication of my ability and understanding, even if its not fully complete yet.

## Incomplete work
#### Wordpress
- I have spent some time trying to understand the best/correct way to add **dynamic html pages** to Wordpress, but haven't got there yet.  As a result, I have not produced the sitemap.html or homepage.html files.
- I have added a method to save the homepage.html file to the server, however I'm missing some Wordpress magic to get it working properly.


- I understand that Wordpress has a built-in pseudo-cron functionality, which I attempted to use to trigger the automatic crawl, but didn't get it quite right.

- I have added a test, (but it is failing at the moment!)


## How your solution achieves the admin’s desired outcome per the user story

While I have not yet produced the sitemap.html or homepage.html pages, I hope that what I have done makes sense.

I have also not yet configured a cron task for automatically triggered crawling.

***

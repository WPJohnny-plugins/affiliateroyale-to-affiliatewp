DESCRIPTION:

This is not a WordPress plugin. It's just a script that migrates data from the Affiliate Royale to AffiliateWP. (Basically, it exports data from AR, converts it, and uploads it back to AWP.)

INSTRUCTIONS:
1. Upload the script to your WordPress directory.
2. Edit the script, changing "yoursite.com" (around line 40) to your site url. Also, comment out STEPS 2 & 3. 
3. Run the script once. (https://yoursite.com/affiliateroyale-to-affiliatewp.php)
4. It will show a success message, or throw a timeout error.
5. If STEP 1 is successful, repeat for STEPS 2 and 3 but switching the comments.
6. You should be done after three runs.
7. Please remove the file after use as it's a security vulnerability if left on your server.
8. Look at your AffiliateWP info and see if it's correct. If yes, you can deactivate AffiliateRoyale. 

NOTES:
- You should save backup your database before doing any of this (just to be safe).
- You may have to increase your server PHP execution time and memory limits. I increased mine to 1800 seconds and 512MB to be safe. Of course, different data sizes and servers may need different limits.
- Do not run it more than once or else it will create duplicate data.
- If there's enough demand for it, I might turn it into an official plugin.
- You should configure AWP to use the same affiliate urls as AR so your affiliates don't have to change their links.

DETAILS:
- It worked fine for me and was able to get all the data I wanted on latest of version of Affiliate Royale and latest version of AffiliateWP at the date of JUNE 9, 2020. 
- Affiliate name, commissions earned, etc. For more details, you can read the script code to see all that was migrated
- I did think to make a plugin originally but the two plugins were so different, so I went this simple script route to save time.

DESCRIPTION

This is not a WordPress plugin. It's just a script that migrates data from the Affiliate Royale plugin to AffiliateWP plugin. (Basically, it exports data from AR, then converts it and uploads it back to AWP.)

Instructions:
1. Upload the script to your WordPress directory.
2. Run the script once.
3. It will either error out and tell you if you timed out, or let you know when it's done.

NOTES: 
- You may have to increase your server PHP execution time and memory limits. I increased mine to 1800 seconds and 512MB to be safe. Of course, different data sizes and servers may need more or less time.
- Do not run it more than once or else it will create duplicate data.
- If there's enough demand for it, I might turn it into an official plugin.
- You should configure AWP to use the same affiliate urls as AR so your affiliates don't have to change their links.

DETAIL

It worked fine for me and was able to get all the data I wanted on latest of version of Affiliate Royale and latest version of AffiliateWP at the date of JUNE 9, 2020. 

Affiliate name, commissions earned, etc. For more details, you can read the script code to see all that was migrated


While checking the db, I noticed some relationship (between affiliate, transaction, referral, visit) were not matched well, so I doubted and confused many things. 

Finally I understand why the relationships were not matched well, 

because you changed Affiliate system two times. 

I had to know the long story before started this work, 

if so, I could save time and could be confident about my work. 

I will try once more if I can find paid data and migrate, but I think maybe current work would be the maximum result




1. We could not do this work by developing a custom plugin. Reason: Two plugins were working differently completely. There was no chance to make a addon for AW. 2. I made one script file (process.php) and put in the WP site root directly and run. The script has 3 parts (I can tell each part is each step to migrate) to migrate data. After run the php script, I removed the file from WP folder and disabled AR plugin. 3. All your AW test data were removed because of my migration. You can cancel the recurring payment of your test in your payment system. And I need you to do full test based on current AW.

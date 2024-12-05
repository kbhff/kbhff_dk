# Modifying JS

Do **NOT** edit the seg_desktop.js, seg_smartphone.js and seg_unsupported.js files found in the /js folder. 
These files are the result of the build process. Any changes made to these files will be lost upon next build.

To modify or build JS, please contact the dev team at KBHFF.


## Further notes on modifying JS

To modify the site JS, please make your changes in the files found in /js/lib/*
To enable the site dev state, which will include the source JS files and allow you to circumvent the build process, 
then just run the site with kbhff.local?dev=1. The dev parameter works on all pages and remains active for the session 
or until you turn it of by requesting a page with ?dev=0


JS files are split into a desktop and smartphone segment. This provides a more specific JS with less overhead.
The relevant segment will automatically be included by the system, depending on the visiting device. If you want to overrule
the selected segment, this can be done by adding ?segment=desktop or ?segment=smartphone to any page of the site.

Secondly the JS files are seperated into _core_ files, holding the generalised JS which affects many pages, 
and _types_ containing the more specific type JS typically only affecting a subset of the site, or even just one page.


When modifying core JS files be thorough in your testing.
When modifying types JS files be thorough in your selector usage to make sure the JS only applied to the desired pages.
When adding new files to the repo, consider you naming so it reflects the target of you JS.

New files are included via the seg_xxx_include.js


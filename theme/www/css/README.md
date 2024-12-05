# Modifying CSS

Do **NOT** edit the seg_desktop.css, seg_smartphone.css and seg_unsupported.css files found in the /css folder. 
These files are the result of the build process. Any changes made to these files will be lost upon next build.

To modify or build CSS, please contact the dev team at KBHFF.


## Further notes on modifying CSS

To modify the site CSS, please make your changes in the files found in /css/lib/*
To enable the site dev state, which will include the source CSS files and allow you to circumvent the build process, 
then just run the site with kbhff.local?dev=1. The dev parameter works on all pages and remains active for the session 
or until you turn it of by requesting a page with ?dev=0


Firstly CSS files are split into a desktop and smartphone segment. This provides a more specific CSS with less overhead.
The relevant segment will automatically be included by the system, depending on the visiting device. If you want to overrule
the selected segment, this can be done by adding ?segment=desktop or ?segment=smartphone to any page of the site.

Secondly the CSS files are seperated into _core_ files, holding the generalised CSS which affects all (or almost all) pages, 
and _types_ containing the more specific type CSS typically only affecting a subset of the site, or even just one page.


When modifying core CSS files be thorough in your testing.
When modifying types CSS files be thorough in your selector usage to make sure the CSS only applied to the desired pages.
When adding new files to the repo, consider you naming so it reflects the target of you CSS.

New files are included via the seg_xxx_include.css


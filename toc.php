<?php
 require_once(getenv("HARDCOPY_SRCINC_MAIN"));

 $Title = "Organized Unix Coding Handbook (Ouch)";
 $Cover = "abs-cover";

 hcAddPages("ch01-limit-api-use");
 hcAddPages("ch02-io");
 hcAddPages("ch03-proc-ctrl");
 
 hcAddPages("ch04-perms");

 // No good ideas for the following:
 # hcAddPages("ch03-socket");
 # hcAddPages("ch04-maths");

 hc_StartAnnexes();
 
 hcAddPages("annex-a-references");

 hcFinish();

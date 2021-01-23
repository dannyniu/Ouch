<?php // It's optional for cover page to test for ``hcPageBegin()'' ?>
<div class=booktitle><?= $Title ?></div>

<div class=abstract>
  <p>
    Many people learn programming on Unix, either shell or system interface,
    using various on-line sources, buying books, or through school.
    
    From my learning experience, I can tell that information are scattered -
    Online resources often focus on single aspect, the standard treats subjects
    not in logical, but in alphabetical order. And for books, information can
    sometimes be outdated, and code examples often don't lead to
    useful applications.
  </p>
  
  <p>
    This document set does not intend to replace them, and it cannot
    replace them. Instead, it's supposed to supplement those resources for
    readers interested in learning Unix.

    Much of the content here revolves around explaining "how it works",
    I choose such flow of presentation so as to provide a better understanding
    for the programmers as to "how to make my program work".
  </p>

  <p>
    It's noted while reviewing for improvements, much of the content overlaps
    with that of chapter 7 "UNIX System Calls" of the book
    "The Unix Programming Environment"<sup><?= hcNamedHref("ref-tupe") ?></sup>.
    I believe that the main reason for this is because the essential primitives
    needed for writing most of the functional programs remains much the same
    over this many years.
  </p>

  <p>
    Suggestions and ideas for improvements are <b>ABSOLUTELY WELCOM</b>, and
    may be communicated as Issues on GitHub at
    <a target="_blank" href="https://github.com/dannyniu/Ouch/issues">here</a>.
  </p>
  
  <p>
    <b>Copyrights:</b>
    This document set is released to the public domain.
    The excerpts used in this document set from other copyrighted works
    must strictly follow the rules of fair use, materials that violates
    copyrights must be removed and/or replaced.
  </p>
</div>

<p style="text-align: center;">
  Last-Revised: <?= date("Y-m-d") ?>
</p>

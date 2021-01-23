<?php
 require_once(getenv("HARDCOPY_SRCINC_MAIN"));

 $hdr = hc_H1("References");

 $ref = [];
 $ref['STDC'] = hcNamedAnchor("[STDC]", "ref-std-c");
 $ref['SVID'] = hcNamedAnchor("[SVID]", "ref-svid");
 $ref['POSIX'] = hcNamedAnchor("[POSIX]", "ref-posix");
 $ref['Unix8'] = hcNamedAnchor("[Unix8]", "ref-unix-8th");
 $ref['TUPE'] = hcNamedAnchor("[TUPE]", "ref-tupe");

 if( !hcPageBegin() ) return;
?>
<?= $hdr ?>

<ul>
  <li>
    <?= $ref['STDC'] ?> ISO/IEC-9899 Programming Languages - C
    (Available at <?= hcURL("https://www.iso.org/standard/74528.html") ?>)
  </li>

  <li>
    <?= $ref['SVID'] ?> System V Interface Definition
    (Available at <?= hcURL("http://www.sco.com/developers/devspecs/") ?>)
  </li>

  <li>
    <?= $ref['POSIX'] ?>
    ISO/IEC/IEEE 9945 Information Technology -
    Portable Operating System Interface
    (Available at
    <?= hcURL("https://www.iso.org/standard/50516.html") ?>,
    <?= hcURL("https://ieeexplore.ieee.org/document/7582338/") ?>, and
    <?= hcURL("https://publications.opengroup.org/standards/unix/t101") ?>)
  </li>

  <li>
    <?= $ref['Unix8'] ?> Manual Pages for Research Unix Eighth Edition
    (Available at <?= hcURL("http://man.cat-v.org/unix_8th/") ?>)
  </li>

  <li>
    <?= $ref['TUPE'] ?> The Unix Programming Environment
    (Info At <?= hcURL(
              "http://books.cat-v.org".
              "/computer-science/unix-programming-environment/") ?>)
  </li>
</ul>

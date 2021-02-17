<?php
 require_once(getenv("HARDCOPY_SRCINC_MAIN"));

 $hdr = hc_H1("Using only a Limited Subset of APIs");

 $hdr_01 = hc_H2(
   "Problems of Multi-Threaded Programs ".
   "with Many Library Dependencies");

 if( !hcPageBegin() ) return;
?>
<?= $hdr ?>

<p>
  In most cases, a program can be very simple. It's true that many modern-day
  simple programs aren't implemented with C, but for the ones that do, they
  enjoy the low-level efficiency and datum-level expressiveness offerred by C.
  A simple program can also be part of a larger "team" - which is the essence
  of the Unix philosophy of many small "dedicated" programs achieving a
  big complex function.
</p>

<p>
  The standard library of many languages, including C, have enough APIs
  to make simple programs that do useful works. The C standard
  <sup><?= hcNamedHref("ref-std-c") ?></sup>
  have a good organization of standard APIs in chapter 7 and annex B.
  The POSIX<sup><?= hcNamedHref("ref-posix") ?></sup> API is not a
  strict superset of the C API, for example, C11 has a different
  multi-threading API than POSIX, and C11 has atomic functions
  which POSIX currently lacks.
</p>

<p>
  Using only a limited subset of API to write simple programs has the benefit
  of avoiding operational conflict with other APIs and other parts of the API,
  this is especially true with multi-threaded programs. 
</p>

<?= $hdr_01 ?>

<p>
  Traditionally, Unix wasn't designed with support for
  multi-threaded processes, and as such, many design elements of the
  multi-threading paradigm required auxiliary interfaces. For example,
  the standard specifies <code>pthread_atfork(3)</code> for multi-threaded
  programs with threads that spawns child processes, <code>openat(2)</code> and
  other <code>*at</code> functions to support per-thread working directory.
</p>

<p>
  Threads in a multi-threaded process shares states global to the process,
  such as signal handling action. Networking libraries in particular deserve
  a special treatment here.
</p>

<p>
  Suppose a program use multi-threading to issue and manage multiple
  network requests, it will be almost impossible for the kernel to identify
  the thread that should receive <code>SIGURG</code>, when a TCP packet with
  "urgent" bit is received (on the 3 implementations I surveyed, Darwin,
  FreeBSD, and Linux, all sends <code>SIGURG</code> to the main thread).
  In reality, the "urgent" flag is obsolete, and networking libraries
  are almost unusable with multi-threaded programs if they don't support
  "connection pool".
</p>

<p>
  If additionally, a multi-threaded networking program spawns a process that
  executes external program, there needs a way for the library to set the
  <code>FD_CLOEXEC</code> flag atomically for new connections, otherwise,
  file descriptor leakage occurs - it is therefore, preferable, to always
  manage I/O handles on the main thread.
</p>

<p>
  Situations are slightly better for pure-computation libraries such as
  media transcoder as these types of libraries have far less kernel liaison
  to do. However, in many cases, they still can't avoid loading large
  (precomputed) data files, and sometimes, updating variable information.
</p>

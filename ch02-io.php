<?php
 require_once(getenv("HARDCOPY_SRCINC_MAIN"));

 $hdr = hc_H1("Input/Output");
 $hdr_01 = hc_H2("Overview of Basic I/O Functions");
 $hdr_02 = hc_H2("I/O Semantics of Regular Files and Pipe/FIFO");

 if( !hcPageBegin() ) return;
?>
<?= $hdr ?>

<?= $hdr_01 ?>

<p>
  In this section, we presents the <em>most</em> commonly used I/O functions
  in categories, and briefly discuss their basic semantics. 
  In the following declarations:
</p>

<ul>
  <li>
    <code>fd</code> refers to a file descriptor number of type
    <code>int</code>
  </li>
  
  <li>
    <code>buf</code> refers to a pointer to a region of data of type
    <code>void *</code> or <code>const void *</code>
  </li>
  
  <li>
    <code>len</code> refers to the length of said data of type
    <code>size_t</code>
  </li>

  <li>
    <code>str</code> refers to a NUL-terminated string of type
    <code>char *</code> or <code>const char *</code>
  </li>
  
  <li>
    <code>mode</code> refers to file permission bit modes of type
    <code>mode_t</code>
  </li>
</ul>

<p>
  SVID<sup><?= hcNamedHref("ref-svid") ?></sup> groups its
  "Base OS Service Routines" into 3 groups - the 1st are meant to
  "fulfill the needs of most application programs", the 2nd for ones with
  "some special needs", and 3rd for
  "other components of the Base System" and are
  "not expected to be used by application programs". Unsuprisingly,
  C standard I/O functions are in the 1st group, and <code>read(2)</code>
  and <code>write(2)</code> are in the 2nd group.
</p>

<p>
  However there is still some functions that work with file descriptors
  in group 1 -
  <code>{fchdir,fchmod,fchown}(2)</code>,
  <code>{fcntl,ioctl}(2)</code>,
  <code>fileno(3)</code>,
  <code>pipe(2)</code>,
  etc.
</p>

<code><pre>// input/output functions declarations.

ssize_t read(fd, buf, len);
ssize_t write(fd, buf, len);</pre></code>

<p>
  These IO functions returns the number of bytes read/written.
  Unlike their standard I/O counter parts, a return less than request
  doesn't always signify EOF or failure. However, as a special case, a
  return of 0 by <code>read(2)</code> is, according to
  8th edition Unix manual<sup><?= hcNamedHref("ref-unix-8th") ?></sup>,
  "conventionally interpreted as end of file".
</p>

<p>
  Some careful readers may have noticed by now that, the current standard
  doesn't mention return value 0 as EOF in the "RETURN VALUE" section of
  the <code>read(2)</code> interface. This is true, and the mention of
  I/O semantics are spread all over the description for the interface.
  Here, we will take a different approach - the interface will be described
  abstractly, and I/O semantics will be described separately for different
  types of I/O handles such as regular file, pipe, <!-- and sockets?, --> etc.
</p>

<code><pre>// file descriptor functions.

int close(fd);
int dup(fd);
int dup2(fd, fd);

int fcntl(fd, int cmd, ...);
int ioctl(fd, int req, ...);
</pre></code>

<p>
  These functions operates on file descriptors.
  <code>fcntl(2)</code> may additionally operate on open file descriptions.
  <code>ioctl(2)</code> sends control commands that aren't representable as
  regular I/O functions. Because control commands are specific to I/O objects
  specific to implementations, the <code>ioctl(2)</code> function is being
  removed from the standard along with STREAMS specified associated with it.
</p>

<?= $hdr_02 ?>

<code><pre>// creating file descriptors to regular files, and pipe/fifo.

int open(str, int, ...);
int creat(str, mode);

int mkfifo(str, mode);
int pipe(int fd[2]);</pre></code>

<p>
  The <code>open(2)</code> function creates a file descriptor referring to
  a file in the filesystem, this can be any type of file supported by the
  implementation, but we're only interested in regular files and FIFOs.
  The <code>creat(2)</code> have only a subset of <code>open(2)</code>'s
  functionalities, but being widely used, it's retained in the standard.
  The <code>mkfifo(2)</code> function creates a FIFO file (a.k.a. named pipe)
  in the filesystem, where as <code>pipe(2)</code> returns 2 ends of an
  anonymous pipe.
</p>

<p>
  For regular files, the I/O semantics of the <code>read(2)</code> and
  <code>write(2)</code> functions are pretty much the same as that of
  standard I/O <code>fread(3)</code>, and <code>fwrite(3)</code>, in that:
</p>

<ul>
  <li>
    Successful writes will return <code>len</code>, and
    any return less than it indicates no data can be written
    (storage capacity reached, device disconnect).
  </li>

  <li>
    Reads will return <code>len</code> as long as there's input data available.
    Reads less than <code>len</code> indicates EOF have been reached, and
    will be indicated in the next read with a return of 0.
  </li>
</ul>

<p>
  For pipe/fifo, the write semantic is not much different from that of
  standard I/O; the read semantic is different due to data availability, but
  still follows the EOF convention.
</p>

<ul>
  <li>
    Successful writes will return <code>len</code>, and
    any return less than it indicates that no other process (or thread) is
    reading from it (and may indicate that the entire pipeline should be
    shut down and no more data should be produced by the writing process). 
  </li>

  <li>
    Additionally for write, if all reading ends had been closed, a
    <code>SIGPIPE</code> will be sent to the thread requesting the write,
    and <code>errno</code> will be set to <code>EPIPE</code> on that thread.
  </li>

  <li>
    Successful reads return the number of bytes been read, which may be
    less than <code>len</code> if not enough data is immediately available.
  </li>
</ul>

<p>
  These are of course, based on the assumption that the
  <code>O_NONBLOCK</code> flag is clear.
  In simple programs, this flag is almost never needed.
</p>

<p>
  The standard fully specifies every aspects of <code>read(2)</code> and
  <code>write(2)</code>, as well as their interaction with other I/O-related
  functions such as <code>close(2)</code>.
</p>

<p>
  The semantics of pipe/fifo gets more complicated if there's multiple reader
  and/or multiple writer. The standard goes on to specify characteristics
  such as atomic data size, etc. to improve behavioral portability.
  The way these characteristics are specified makes it difficult for
  portable applications to do much that is useful, and multi-reader-write
  model should almost always be avoided.
</p>

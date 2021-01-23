<?php
 require_once(getenv("HARDCOPY_SRCINC_MAIN"));

 $hdr = hc_H1("Process Control");

 $hdr_01 = hc_H2("Elements of Process Control");
 $hdr_02 = hc_H2("Process Control Life Cycle");
 $hdr_03 = hc_H2("Process Control Functions");

 if( !hcPageBegin() ) return;
?>
<?= $hdr ?>

<p>
  With C, it's entirely possible to write any function/algorithm/etc. in it,
  so why would we want separate processes? Well, the Unix philosophy says
  to write small "dedicated" programs that do one thing well, and combine them
  to accomplish complex tasks. Here's several ways to use multiple processes:
</p>

<ol>
  <li>
    Outsource some processing to external programs, because they are
    better suited for the task. <i>AND</i> wait for them to finish.
    As is the case for the <code>system(3)</code> function. <i>OR</i>
  </li>

  <li>
    Outsource to and work with external program to arrive at
    a common a common result. As is the case with shell pipelines. <i>OR</i>
  </li>

  <li>
    Outsource it, then do own work, then wait and collect the result from
    the external program. As is the case with background processes in shell,
    or a "special effect" plugin in digital audio workstation or video editing
    softwares.
  </li>

  <li>
    Dispatch and leave workloads to external programs, as is the case of
    network servers.
  </li>
</ol>

<p>
  Now. Even though SVID says, quote:
</p>

<blockquote>
  ... application programs, when possible, should use the routine
  <code>system()</code> rather than the routines <code>fork()</code>
  and exec because it is easier to use and supplies more functionality.
</blockquote>

<p>
  In reality, application often require the capability to send
  argument strings as vectors, rather than escaping them and pass then
  through a shell to the program. This is true even if the command string is
  fixed, because escaping the &lt;double-quote&gt; makes writing command string
  more difficult; it is <em>especially</em> true with applications that often
  take external input such as dynamic web servers.
</p>

<?= $hdr_01 ?>

<p>
  Apart from creating and terminating processes, process control also involve
  auxiliary works such as establishment of communication channel,
  signal handling, etc.
</p>

<p>
  Intuitively, the creation of a independent process involves only passing
  arguments (sometimes also environments) through the system call to the
  kernel, using some kind of function such as <code>spawnve(2)</code>
  (hypothetically). Such <code>spawn</code> functions do exist, but in
  Microsoft Windows C Runtime Library, and are limited in functionality
  compared to the more native Win32 system library functions such as
  <code>CreateProcessA</code>.
</p>

<p>In Unix, in order to allow greater flexibility, a limited form of a
  full execution environment is first created using the <code>fork(2)</code>
  call, then the program is able to change the execution environment
  for the child with arbitrary application code. When the execution environment
  (including parameters such as file descriptors, ignored signals, etc.) is
  established, one of the <code>exec</code> functions can be called to start
  the new program.
</p>

<p>
  Notice I said "limited form of a full execution environment", this is
  because some states are inconsistently shared (or not shared) with the
  parent process; and for operating systems with richer functionality, some
  extended functionality may break when used in a newly-forked child process.
  The execution environment becomes "unlimited" when the process calls
  one of the <code>exec</code> functions.
</p>

<p>
  The next revision of POSIX is expected to introduce <code>_Fork(2)</code>
  for the purpose of decreasing execution overhead and improving thread and
  signal safety. By then, <code>fork(2)</code> will no longer be mandated
  async-signal-safe, although many implementation currently with only
  implementation for <code>fork(2)</code> will continue to support it as a
  (supposedly if not actually) async-signal-safe function.
</p>

<p>
  The previous chapter talked about I/O and it as a communication channel
  using pipe/fifo. Now, we'll look at how processes can interact with
  each other using signals, and how parent processes can query the status
  of its childs, just as we dive into the process control life cycle.
</p>

<p>
  Section 2.4.1 "Signal Geration and Deliver" of the "System Interfaces" volume
  is a thorough summary of the behaviors of signals in Unix. In brief:
</p>

<ul>
  <li>
    Signals can be generated and sent to processes (or a thread within a
    process if the condition for the signal to occur is local to the thread
    (e.g. integer division by 0)).
  </li>

  <li>
    Signal actions can be one of "system default", "ignored", "caught", and
    is a property associated with a process.
  </li>

  <li>
    Signals can be blocked using "signal mask". And in a mutli-threaded
    process, on a per-thread basis. (That is, signal mask is a property
    associated with a thread.)
  </li>
  
  <li>
    When a signal is sent to a multi-threaded process, the one thread
    <code>sigwait</code>ing the signal, or a thread not blocking it
    (but not both) will receive the signal. If no such thread exists,
    the signal will be "pending" delivery (standard's terminology).
  </li>

  <li>
    -- Some other "obvious" minor details are also specified
    in the standard. --
  </li>
</ul>

<p>
  Apart from the above, in section 2.4.4 "Signal Effects on Other Functions"
  also spends a lengthy paragraph to describe how some functions (mostly
  system calls) may be "interrupted".
</p>

<p>
  In the model of the C language, a process has an exit status, produced as
  the return value of the initial <code>main</code> function, or argument to
  one of the <code>exit</code> functions. The value of this exit status has
  a set of conventions in Unix, and is used extensively when the overhead of
  communicating it (often just it) through other means such as I/O output is
  more expensive.
</p>

<p>
  When the execution status of a process changes in Unix (such as when it's
  terminated, stopped, continued, etc.) its parent can collect such information
  using one of the <code>wait</code> functions.
</p>

<p>
  When a child process terminates, if its parent is not ignoring
  <code>SIGCHLD</code>, status information is generated for the child process,
  which immediately becomes a zombie process until its parent collects its
  status or parent exits. If a parent process doesn't collect its childs'
  status information and doesn't ignore <code>SIGCHLD</code>, zombie processes
  piles up in the kernel and exhausts the system.
</p>

<?= $hdr_02 ?>

<p>
  The following ASCII art illustrates the life cycle of a process.
</p>

<pre>## The Life Cycle of a Process ##

                          V---------------------------------*
{fork}---->[running]--+-->(received signal)--+-->[stopped]--^
           ^--+--------------[continued]-----+
              ^       |                      +-->[terminated]
              |       |                      ^
              |       +-->(invokes syscall)--+
              *------------------------------*
</pre>

<p>
  The following ASCII art illustrates the life cycle of the
  control of one process.
</p>

<pre>## One Process Control Life Cycle ##

{parent}->(fork-+->)->{child}
                |        |
          *-----*        V
          |          [running]
          |              |
          V              V
 *--->[running]        (exec)
 |        |              |
 |        |              V
 |        |          [running]<-------------------*
 |        |              |                        |
 |        V              V                        |
 | [receive signal]<-+-(execution state change)   |
 |        |          |   |                        |
 |        |      *---*   +-->[stopped,continued]--*
 |        |      |       |
 |        V      |       V
 *------(wait)<--+--[terminate]
</pre>

<?= $hdr_03 ?>

<code><pre>// process creation.

pid_t fork();</pre></code>

<p>
  The <code>fork(2)</code> function duplicates the calling process, then
  returns the PID of the new process to the parent, and 0 to the child.
  If failure occurs, -1 is returned to the parent and no new process is
  created.
</p>

<code><pre>// program execution.

// function declarations,     // argument passing.
int execl  (path, arg0, ...); // path, arg&lt;0...n&gt;;
int execle (path, arg0, ...); // path, arg&lt;0...n&gt;, envp;
int execlp (file, arg0, ...); // same as that of execl.
int execv  (path, argv);
int execve (path, argv, envp);
int execvp (file, argv);

// The following is a new mandatory feature in Issue 7.
int fexecve(fd, argv, envp);</pre></code>

<p>
  The <code>exec</code> family of functions replace the current process image
  with that of a program file and executes it with the passed argument(s)
  (and environment variable(s)). 
</p>

<code><pre>// exit functions for normal termination.

void exit(int);
void _Exit(int);
void _exit(int);</pre></code>

<p>
  These functions terminates the process with the specified exit status.
  <code>exit(3)</code> calls the functions registerred with
  <code>atexit(3)</code>, while <code>_Exit(3)</code> and <code>_exit(2)</code>
  don't.
</p>

<code><pre>// collect process status.

pid_t wait(int *);
pid_t waitpid(pid_t, int *, int options);

// The following is a new mandatory feature in Issue 7.
int waitid(idtype, id, siginfo_t *, options);</pre></code>

<p>
  These functions collect the s status or state-change information for
  some certain process.
</p>

<code><pre>// signal sending.

int kill(pid, sig);</pre></code>

<p>
  This function sends signal to the specified process or process group.
  Some other functions in the standard may have overlapping functionalities.
</p>

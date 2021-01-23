<?php
 require_once(getenv("HARDCOPY_SRCINC_MAIN"));

 $hdr = hc_H1("Permissions / Various IDs Associated with a Process");
 $tbl_01 = hc_Table("Functions for Setting IDs");
 $tbl_02 = hc_Table("Functions for Querying IDs");

 if( !hcPageBegin() ) return;
?>
<?= $hdr ?>

<p>
  A process has an <i>User ID</i> (UID) and a <i>Group ID</i> (GID), both are
  further subdivided into <i>Real</i> and <i>Effective</i> UIDs and GIDs.
  The distinction of real and effective IDs had been present, all the way back
  to around 1970s since the 1st edition Unix, to support secure system
  administration. Here's how.
</p>

<p>
  When one of the <code>exec</code> functions loads a program from a file,
  if the file has set-uid (likewise, set-gid) bit set, the effective
  user (group) ID is set to that of the file; the real user (group) ID
  remains to allow programs like <code>su(1)</code> and <code>passwd(1)</code>
  to identify the entity that executed the program so that they can securely
  perform actions that require superuser privileges.
</p>

<p>
  Current standard also specifies the <i>Saved User ID</i> and
  <i>Saved Group ID</i>. These were from System V. These IDs are
  set to the (original) Effective User and Group IDs by one of the
  <code>exec</code> functions.
</p>

<p>
  The 1st thing this allows, is "Job Control" by shells - if a descendant
  process has its IDs changed, it will still be able to receive the signals
  sent by the shell. The shell sends signals using the <code>kill(2)</code>
  call, whereby the the standard specifies the implementations to checks
  if the receiving process (or the process in the receiving process group)
  have a real or saved UID matching the real or effective UID of the sender.
  Older BSD systems without this feature allowed processes to send signals
  to its descendants regardless of their IDs - a semantic which didn't make it
  into the standard.
</p>

<p>
  The 2nd thing enabled by the saved IDs feature, is that a process can switch
  between real and saved IDs. For example:
</p>

<ol>
  <li>
    after a process receives a privileged effective UID
    (e.g. through <code>exec</code>) it can then,
  </li>

  <li>
    set its saved and effective UID to the privileged UID, whilest
    leaving the real UID to the original (unprivileged) UID using
    <code>setreuid(2)</code>, then
  </li>

  <li>
    switch to (the unprivileged) real UID when performing non-privileged tasks,
    and to (the privileged) saved UID when performing restricted tasks.
  </li>
</ol>

<p>
  Additionally, a process has a set of <i>Supplementary Group IDs</i>.
  Supplementary GIDs were introduced in 4.2BSD circa 1983, before this,
  a process can only be in 1 group at a time, and must switch between
  groups to attain different membership permissions. In modern implementation,
  a process can have as many groups as up to a certain fixed limit.
  The standard specifies <code>getgroups(2)</code> to query the supplementary
  GIDs, but currently not any common-place function (e.g.
  <code>setgroups(2)</code> or <code>initgroups(3)</code>) to set it.
</p>

<p>
  Heres a summary of functions of functions for querying and setting
  various IDs:
</p>

<table>
  <caption><?= $tbl_01 ?></caption>
  <thead>
    <tr>
      <th>Name</th>
      <th>Real/Effective/Saved/Supplementary</th>
      <th>Condition</th>
    </tr>
  </thead>

  <tbody>
    <tr>
      <td rowspan="2"><code>set[ug]id(id)</code></td>
      <td>effective</td>
      <td>
        Unprivileged &and;
        <code>id</code> &in; {Real & Saved [UG]ID}
      </td>
    </tr>
    <tr>
      <td>real & effective & saved</td>
      <td>
        Privileged
      </td>
    </tr>

    <tr>
      <td><code>sete[ug]id(id)</code></td>
      <td>effective</td>
      <td>Privileged &or; id &in; {Real & Saved [UG]ID}</td>
    </tr>

    <tr>
      <td><code>setreuid(ruid, euid)</code></td>
      <td>real & effective & <i>conditionally</i>: saved</td>
      <td>
        <ul>
          <li>
            effective &DoubleLeftArrow; Privileged &or;
            <code>euid</code> &in; {Real & Effective & Saved UID}
          </li>
          <li>
            real &DoubleLeftArrow; Privileged &or; <code>ruid</code>
            Under condition not specified by the standard
          </li>
          <li>
            saved (to <code>euid</code>) &DoubleLeftArrow;
            real UID would be set &or; effective UID would be
            set to a value &ne; saved UID
          </li>
        </ul>
      </td>
    </tr>

    <tr>
      <td><code>setregid(rgid, egid)</code></td>
      <td>real & effective & <i>conditionally</i>: saved </td>
      <td>
        <ul>
          <li>
            effective &DoubleLeftArrow; Privileged &or;
            <code>egid</code> &in; {Real & Saved GID}
          </li>
          <li>
            real &DoubleLeftArrow; Privileged &or;
            <code>rgid</code> = Saved GID.
          </li>
          <li>
            saved (to <code>egid</code>) &DoubleLeftArrow;
            real GID would be set &or; effective GID would be
            set to a value &ne; real GID
          </li>
        </ul>
      </td>
    </tr>
  </tbody>
</table>

<table>
  <caption><?= $tbl_02 ?></caption>
  <thead>
    <tr>
      <th>Name</th>
      <th>Real/Effective/Saved/Supplementary</th>
    </tr>
  </thead>

  <tbody>
    <tr>
      <td><code>get[ug]id</code></td>
      <td>real</td>
    </tr>
    <tr>
      <td><code>gete[ug]id</code></td>
      <td>effective</td>
    </tr>
  </tbody>
</table>

<?
 include_once("../globals.php");
 include_once("$srcdir/sql.inc");
 include_once("$srcdir/pid.inc");
 include_once("$srcdir/patient.inc");

//here, we lock the patient data table while we find the most recent max PID
//other interfaces can still read the data during this lock, however
sqlStatement("lock tables patient_data read");

$result = sqlQuery("select max(pid)+1 as pid from patient_data");

// TBD: This looks wrong to unlock the table before we have added our
// patient with its newly allocated pid!
//
sqlStatement("unlock tables");
//end table lock
$newpid = 1;

if ($result['pid'] > 1)
  $newpid = $result['pid'];

setpid($newpid);

if($pid == NULL) {
  $pid = 0;
}

//what do we set for the public pid?
if (isset($_POST["pubpid"]) && ($_POST["pubpid"] != "")) {
  $mypubpid = $_POST["pubpid"];
} else {
  $mypubpid = $pid;
}

if ($_POST['form_create']) {

 $form_fname = ucwords(trim($_POST["fname"]));
 $form_lname = ucwords(trim($_POST["lname"]));
 $form_mname = ucwords(trim($_POST["mname"]));

 newPatientData(
  $_POST["db_id"],
  $_POST["title"],
  $form_fname,
  $form_lname,
  $form_mname,
  "", // sex
  "", // dob
  "", // street
  "", // postal_code
  "", // city
  "", // state
  "", // country_code
  "", // ss
  "", // occupation
  "", // phone_home
  "", // phone_biz
  "", // phone_contact
  "", // status
  "", // contact_relationship
  "", // referrer
  "", // referrerID
  "", // email
  "", // language
  "", // ethnoracial
  "", // interpreter
  "", // migrantseasonal
  "", // family_size
  "", // monthly_income
  "", // homeless
  "", // financial_review
  "$mypubpid",
  $pid
      // providerID
      // genericname1
      // genericval1
      // genericname2
      // genericval2
      // phone_cell
      // hipaa_mail
      // hipaa_voice
      // squad
 );

 newEmployerData($pid);
 newHistoryData($pid);
 newInsuranceData($pid, "primary");
 newInsuranceData($pid, "secondary");
 newInsuranceData($pid, "tertiary");

 // Set referral source separately because we don't want it messed
 // with later by newPatientData().
 if ($refsource = trim($_POST["refsource"])) {
  sqlQuery("UPDATE patient_data SET referral_source = '$refsource' " .
   "WHERE pid = '$pid'");
 }
}
?>
<html>
<body>
<script language="Javascript">
<?
 if ($alertmsg) { 
  echo "alert('$alertmsg');\n";
 }
 if ($GLOBALS['concurrent_layout']) {
  echo "window.location='$rootdir/patient_file/summary/demographics.php?" .
   "set_pid=$pid&is_new=1';\n";
 } else {
  echo "window.location='$rootdir/patient_file/patient_file.php?set_pid=$pid';\n";
 }
?>
</script>

</body>
</html>

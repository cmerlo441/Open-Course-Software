<?php

$no_header = 1;
require_once( './_header.inc' );

$tables = array( );
$data = array( );

$tables[ 'assignment_documents' ] =
    "CREATE TABLE `assignment_documents` (
    `id` int(11) NOT NULL auto_increment,
    `assignment` int(11) NOT NULL,
    `type` varchar(100) NOT NULL,
    `name` varchar(100) NOT NULL,
    `size` int(11) NOT NULL,
    `file` longblob NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'assignment_submissions' ] =
    "CREATE TABLE `assignment_submissions` (
    `id` int(11) NOT NULL auto_increment,
    `assignment` int(11) NOT NULL,
    `student` int(11) NOT NULL,
    `time` datetime NOT NULL,
    `submission` longtext,
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'assignment_upload_requirements' ] =
    "CREATE TABLE `assignment_upload_requirements` (
    `id` int(11) NOT NULL auto_increment,
    `assignment` int(11) NOT NULL,
    `filename` varchar(50) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'assignment_uploads' ] =
    "CREATE TABLE `assignment_uploads` (
    `id` int(11) NOT NULL auto_increment,
    `student` int(11) default NULL,
    `assignment_upload_requirement` int(11) default NULL,
    `filename` varchar(50) default NULL,
    `filesize` int(11) default NULL,
    `filetype` varchar(50) NOT NULL,
    `datetime` datetime default NULL,
    `file` longblob,
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'assignments' ] =
    "CREATE TABLE `assignments` (
    `id` int(11) NOT NULL auto_increment,
    `grade_type` int(11) NOT NULL,
    `section` int(11) NOT NULL,
    `posted_date` datetime NOT NULL,
    `due_date` datetime NOT NULL,
    `title` varchar(50) default NULL,
    `description` text NOT NULL,
    `grade_summary_tweeted` tinyint(4) NOT NULL default '0',
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'attendance' ] =
    "CREATE TABLE `attendance` (
    `id` int(11) NOT NULL auto_increment,
    `student` int(11) NOT NULL,
    `section` int(11) NOT NULL,
    `date` date NOT NULL,
    `presence` int(11) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'attendance_types' ] =
    "CREATE TABLE `attendance_types` (
    `id` int(11) NOT NULL auto_increment,
    `type` varchar(10) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'authors' ] =
    "CREATE TABLE `authors` (
    `id` int(11) NOT NULL auto_increment,
    `first` varchar(25) NOT NULL,
    `middle` varchar(25) NOT NULL,
    `last` varchar(25) NOT NULL,
    `email` varchar(50) NOT NULL,
    `url` varchar(100) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'contact_information' ] =
    "CREATE TABLE `contact_information` (
    `id` int(11) NOT NULL auto_increment,
    `type` varchar(100) NOT NULL,
    `description` text NOT NULL,
    `contact_info` text NOT NULL,
    `sequence` int(11) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'course_x_textbook' ] =
    "CREATE TABLE `course_x_textbook` (
    `id` int(11) NOT NULL auto_increment,
    `course` int(11) NOT NULL,
    `textbook` int(11) NOT NULL,
    `required` tinyint(4) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'courses' ] =
    "CREATE TABLE `courses` (
    `id` int(11) NOT NULL auto_increment,
    `dept` varchar(5) NOT NULL,
    `course` varchar(10) NOT NULL,
    `credits` tinyint(4) NOT NULL,
    `short_name` varchar(25) NOT NULL,
    `long_name` text NOT NULL,
    `prereq` varchar(250) NOT NULL,
    `catalog` text NOT NULL,
    `outline` longtext NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'curves' ] =
    "CREATE TABLE `curves` (
    `id` int(11) NOT NULL auto_increment,
    `grade_event` int(11) NOT NULL,
    `points` int(11) default NULL,
    `percent` double default NULL,
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'drop_lowest' ] =
    "CREATE TABLE `drop_lowest` (
    `id` int(11) NOT NULL auto_increment,
    `course` int(11) NOT NULL,
    `grade_type` int(11) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'grade_events' ] =
    "CREATE TABLE `grade_events` (
    `id` int(11) NOT NULL auto_increment,
    `section` int(11) NOT NULL,
    `grade_type` int(11) NOT NULL,
    `date` date NOT NULL,
    `assignment` int(11) default NULL,
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'grade_types' ] =
    "CREATE TABLE `grade_types` (
    `id` int(11) NOT NULL auto_increment,
    `grade_type` varchar(50) NOT NULL,
    `plural` varchar(50) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'grade_weights' ] =
    "CREATE TABLE `grade_weights` (
    `id` int(11) NOT NULL auto_increment,
    `course` int(11) NOT NULL,
    `grade_type` int(11) NOT NULL,
    `grade_weight` int(11) NOT NULL,
    `collected` tinyint(4) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'grades' ] =
    "CREATE TABLE `grades` (
    `id` int(11) NOT NULL auto_increment,
    `grade_event` int(11) NOT NULL,
    `student` int(11) NOT NULL,
    `grade` int(11) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'holidays' ] =
    "CREATE TABLE `holidays` (
    `id` int(11) NOT NULL auto_increment,
    `date` date NOT NULL,
    `description` varchar(100) NOT NULL,
    `day` tinyint(1) NOT NULL,
    `evening` tinyint(1) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'last_login_growled' ] =
    "CREATE TABLE `last_login_growled` (
    `id` int(11) NOT NULL auto_increment,
    `login` int(11) NOT NULL,
    `datetime` datetime NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'letter_grades' ] =
    "CREATE TABLE `letter_grades` (
  `id` int(11) NOT NULL auto_increment,
  `letter` varchar(3) default NULL,
  `grade` double default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'logins' ] =
"CREATE TABLE `logins` (
  `id` bigint(20) NOT NULL auto_increment,
  `student` int(11) default NULL,
  `datetime` datetime default NULL,
  `address` varchar(16) default NULL,
  `browser` varchar(250) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'mail_from_students' ] =
"CREATE TABLE `mail_from_students` (
  `id` int(11) NOT NULL auto_increment,
  `student_x_section` int(11) default NULL,
  `subject` varchar(100) default NULL,
  `message` text,
  `sent_time` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'mail_to_classes' ] =
"CREATE TABLE `mail_to_classes` (
  `id` int(11) NOT NULL auto_increment,
  `section` int(11) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `sent_time` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'mail_to_students' ] =
"CREATE TABLE `mail_to_students` (
  `id` int(11) NOT NULL auto_increment,
  `student_x_section` int(11) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'ocsw' ] =
"CREATE TABLE `ocsw` (
  `id` int(11) NOT NULL auto_increment,
  `k` varchar(100) default NULL,
  `v` varchar(200) default NULL,
  `q` varchar(200) NOT NULL,
  `advanced` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'office_hours' ] =
"CREATE TABLE `office_hours` (
  `id` int(11) NOT NULL auto_increment,
  `day` int(11) NOT NULL,
  `start` time NOT NULL,
  `end` time NOT NULL,
  `building` varchar(100) NOT NULL,
  `room` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'page_views' ] =
"CREATE TABLE `page_views` (
  `id` int(11) NOT NULL auto_increment,
  `student` int(11) NOT NULL,
  `page` varchar(100) NOT NULL,
  `get_string` varchar(100) NOT NULL,
  `datetime` datetime NOT NULL,
  `referrer` varchar(100) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'pages' ] =
"CREATE TABLE `pages` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(200) NOT NULL,
  `text` text NOT NULL,
  `slug` varchar(200) NOT NULL,
  `last_modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'password_reset_request' ] =
"CREATE TABLE `password_reset_request` (
  `id` int(11) NOT NULL auto_increment,
  `banner_id` varchar(9) NOT NULL,
  `code` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'prof' ] =
"CREATE TABLE `prof` (
  `id` int(11) NOT NULL auto_increment,
  `first` varchar(50) NOT NULL,
  `middle` varchar(50) default NULL,
  `last` varchar(50) NOT NULL,
  `suffix` int(11) default NULL,
  `title` varchar(50) default NULL,
  `department` varchar(200) default NULL,
  `department_url` varchar(200) default NULL,
  `college_name` varchar(200) default NULL,
  `college_address` varchar(200) default NULL,
  `college_url` varchar(200) default NULL,
  `email` varchar(100) NOT NULL,
  `mobile_email` varchar(100) default NULL,
  `username` varchar(50) NOT NULL,
  `password` tinytext NOT NULL,
  `twitter_username` varchar(50) default NULL,
  `twitter_password` varchar(50) default NULL,
  `delicious` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'publishers' ] =
"CREATE TABLE `publishers` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `url` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'quotes' ] =
"CREATE TABLE `quotes` (
  `id` int(11) NOT NULL auto_increment,
  `quote` varchar(500) NOT NULL,
  `attribution` varchar(200) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'reference' ] =
"CREATE TABLE `reference` (
  `id` int(11) NOT NULL auto_increment,
  `filename` varchar(100) default NULL,
  `size` bigint(20) default NULL,
  `type` varchar(100) default NULL,
  `section` int(11) default NULL,
  `uploaded` datetime default NULL,
  `available` tinyint(4) default NULL,
  `file` longblob,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'reference_downloads' ] =
"CREATE TABLE `reference_downloads` (
  `id` int(11) NOT NULL auto_increment,
  `reference` int(11) NOT NULL,
  `student` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'rescheduled_days' ] =
"CREATE TABLE `rescheduled_days` (
  `id` int(11) NOT NULL auto_increment,
  `date` date NOT NULL,
  `day` tinyint(4) NOT NULL,
  `evening` tinyint(4) NOT NULL,
  `follow` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'section_meetings' ] =
"CREATE TABLE `section_meetings` (
  `id` int(11) NOT NULL auto_increment,
  `section` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `start` time NOT NULL,
  `end` time NOT NULL,
  `building` varchar(25) NOT NULL,
  `room` varchar(10) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'sections' ] =
"CREATE TABLE `sections` (
  `id` int(11) NOT NULL auto_increment,
  `course` int(11) NOT NULL,
  `section` varchar(5) NOT NULL,
  `banner` varchar(10) NOT NULL,
  `day` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'semester' ] =
"CREATE TABLE `semester` (
  `name` varchar(50) NOT NULL,
  `start` date NOT NULL,
  `end` date NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'student_x_section' ] =
"CREATE TABLE `student_x_section` (
  `id` int(11) NOT NULL auto_increment,
  `student` int(11) NOT NULL,
  `section` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL default '0',
  `incomplete` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'student_x_verification' ] =
"CREATE TABLE `student_x_verification` (
  `id` int(11) NOT NULL auto_increment,
  `student` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `creation_time` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'students' ] =
"CREATE TABLE `students` (
  `id` int(11) NOT NULL auto_increment,
  `first` varchar(50) NOT NULL,
  `middle` varchar(50) default NULL,
  `last` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `banner` varchar(9) NOT NULL,
  `password` tinytext NOT NULL,
  `verified` tinyint(4) NOT NULL,
  `twitter` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'submission_comments' ] =
"CREATE TABLE `submission_comments` (
  `id` int(11) NOT NULL auto_increment,
  `submission_id` int(11) NOT NULL,
  `who` int(11) NOT NULL,
  `when` datetime NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='who > 0 means student; who == 0 means prof.'";

$tables[ 'suffixes' ] =
"CREATE TABLE `suffixes` (
  `id` int(11) NOT NULL,
  `suffix` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'syllabus_section_customization' ] =
"CREATE TABLE `syllabus_section_customization` (
  `id` int(11) NOT NULL auto_increment,
  `course` int(11) NOT NULL,
  `syllabus_section` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'syllabus_sections' ] =
"CREATE TABLE `syllabus_sections` (
  `id` int(11) NOT NULL auto_increment,
  `section` varchar(50) NOT NULL,
  `default_value` text NOT NULL,
  `editable` tinyint(4) NOT NULL,
  `sequence` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'textbook_x_author' ] =
"CREATE TABLE `textbook_x_author` (
  `id` int(11) NOT NULL auto_increment,
  `textbook` int(11) NOT NULL,
  `author` int(11) NOT NULL,
  `sequence` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$tables[ 'textbooks' ] =
"CREATE TABLE `textbooks` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(100) NOT NULL,
  `subtitle` varchar(100) NOT NULL,
  `edition` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `isbn` varchar(20) NOT NULL,
  `publisher` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

/*
 ****************************************************************************
 * DATA
 ****************************************************************************
 */

$data[ 'attendance_types' ] =
"INSERT INTO `attendance_types` VALUES "
    . "(null,'Absent'),"
    . "(null,'Excused'),"
    . "(null,'Late'),"
    . "(null,'Present')";

$data[ 'grade_types' ] =
"INSERT INTO `grade_types` VALUES "
    . "(null,'Class Participation','Class Participation'),"
    . "(null,'Exam','Exams'),"
    . "(null,'Final Exam','Final Exam'),"
    . "(null,'Homework','Homework'),"
    . "(null,'Lab Assignment','Lab Assignments'),"
    . "(null,'Online Discussion','Online Discussion'),"
    . "(null,'Project','Projects'),"
    . "(null,'Quiz','Quizzes')";
    
$data[ 'ocsw' ] =
"INSERT INTO `ocsw` VALUES "
    . "(null,'words','/usr/share/dict/words','Where is the Unix words file?',1),"
    . "(null,'wf','0','Do you assign the grade of WF (withdraw fail)?',0),"
    . "(null,'block_ie','1','Do you want to block users of Internet Explorer? (Recommended)',0),"
    . "(null,'mobile_email','1','Do you wish to receive mobile e-mail?',0),"
    . "(null,'qotd','1','Would you like to display the Quote of the Day?',0),"
    . "(null,'qotd-email','1','Would you like the Quote of the Day in your outgoing e-mail signature?',0),"
    . "(null,'cc','1','Would you like to receive a copy of e-mail you send?',0),"
    . "(null,'passing_green','0','Do you want to display passing grades in green?',0),"
    . "(null,'passing_red','0','Do you want to display failing grades in red?',0)";

$data[ 'publishers' ] =
"INSERT INTO `publishers` VALUES "
    . "(null,'Addison Wesley','http://www.pearsonhighered.com/'),"
    . "(null,'Course Technology','http://www.cengage.com/coursetechnology/'),"
    . "(null,'McGraw Hill','http://www.mhhe.com/'),"
    . "(null,'Pearson','http://www.pearsonhighered.com/'),"
    . "(null,'Prentice Hall','http://www.pearsonhighered.com/')";
    
$data[ 'syllabus_sections' ] =
"INSERT INTO `syllabus_sections` VALUES "
    . "(1,'Instructor','',0,0),"
    . "(2,'Course','',0,1),"
    . "(3,'Credits','',0,2),"
    . "(4,'Prerequisites','',0,3),"
    . "(5,'Schedule','',0,4),"
    . "(6,'Textbooks','',0,5),"
    . "(7,'Catalog Description','',0,6),"
    . "(8,'Evaluation','The final grade will be determined as follows:',0,17),"
    . "(9,'Course Outline','',0,25),"
    . "(10,'Computer Center','Students should avail themselves of the resources and educational assistance available in the Computer Learning Centers in B 225.',1,7),"
    . "(11,'Disabilities','If you have a physical, psychological, medical, or learning disability that may affect your ability to carry out the assigned coursework, you should contact the staff at the Center for Students with Disabilities, Bldg. U (behind the old College Union), 572-7241. TTY 572-7617. CSD will review your concerns and determine with you what accommodations are necessary and appropriate. All information and documentation are confidential.',1,8),"
    . "(12,'Internet Requirements','Students are required to create an account on the instructor\'s web site. Students will use this account to submit assignments, and to send electronic mail to the instructor.  Students will also use this account to submit programming assignments and view their solutions.\n\nStudents are required to have enabled their NCC-provided e-mail account prior to creating this web site account.  Furthermore, the student must have access to this e-mail account during the creation of the web site account.  Confirmational e-mail messages must be read during the account creation process.  More information is available at <a href=\"http://www.ncc.edu/studentemail\">http://www.ncc.edu/studentemail</a>.',1,9),"
    . "(13,'Homework','Students will be assigned homework assignments at various intervals during the semester. Completion of these assignments will provide valuable practice in answering examination questions. Homework assignments must be submitted via the instructor\'s website, in well-written English prose, except where otherwise indicated. Homework is due at the start of class on the due date. Late assignments are not acceptable. There are no exceptions to this policy.',1,11),"
    . "(14,'Programming Projects','Students will be assigned programming projects during the course of the semester. Completion of these assignments is critical to understanding the course material, and subsequently, to earning a grade for the course. Students will be given a week, at minimum, to complete each assignment. Programming projects are due by 11:59 pm on the due date, and will be submitted at the professor\'s web site. Late assignments will lose 10 points per every 24 hour period, or portion thereof, after the deadline, regardless of weekends, holidays, natural phenomenon, home computer malfunction, family illness, etc. Programming projects are not acceptable more than five days past the deadline. There are no exceptions to this policy.',1,12),"
    . "(15,'Exams','There will be two in-class full-period exams and a final exam.  Exams will be announced ahead of time.  Students must take exams at the time and place indicated, because <b>no makeup exams will be given</b>, unless arrangements are made in advance.  Any student who leaves the classroom during an exam will be considered finished, and all exam materials will be collected.  No student may begin an exam after another student has completed it.  At no time during an exam may a student confer any other people or any printed, written, or electronic materials except as expressly permitted by the professor or another exam proctor.  Any student in violation of this policy will receive a grade of 0 on the exam.',1,15),"
    . "(16,'Withdrawing','The grade of W will only be assigned at the discretion of the instructor, and only if the student explicitly requests the grade from the instructor and receives written permission from the instructor to withdraw before the date of the final exam.\n\nWithdrawals are <b>not</b> automatically granted. Any student who simply stops attending class, or fails to submit a significant amount of work, will earn a grade of F. There are no exceptions to this policy. Withdrawals will <b>only</b> be granted to students who try to complete all the assigned work and miss a minimal amount of class sessions.\n\nNo student who has earned a grade of 0 on any assignment, exam, quiz, or other grading opportunity due to academic dishonesty will be eligible to withdraw from this course.',1,18),"
    . "(17,'Incomplete','The grade of I will only be assigned at the discretion of the instructor, and only if the student experiences an unforeseen, documented medical emergency, which prevents him or her from completing a significant portion of the semester\'s assignments.',1,19),"
    . "(18,'Shared Work Policy','At no time should a student submit any work -- on homework, programming projects, exams, blog posts, or any other graded assignment -- that is not wholly his or her own.  (For instance, you did not write the course textbook; therefore, you are not allowed to copy from it on a homework.)  Failure to adhere to this policy will result in a grade of 0 for each assignment in violation.  Serious breaches of this policy may result in a grade of F for the course.\n\nTo avoid any possible confusion, please read <a href=\"http://plagiarism.org/plag_article_what_is_plagiarism.html\">these guidelines</a> from <a href=\"http://plagiarism.org/\">plagiarism.org</a> that explain what counts as plagiarism.\n',1,20),"
    . "(19,'Identification','As per the College Catalog and the Student Handbook, every student must have valid Nassau Community College identification. Such identification must be presented upon request. Any student in violation of this policy will be removed from class and marked absent. Any student who violates this policy twice will be permanently removed from the class and awarded a failing grade.',1,21),"
    . "(20,'Food and Drink','Food is prohibited in all class meetings.  Beverages are prohibited in all computer labs.  Any student in violation of this policy will be removed from class and marked absent.  Any student who violates this policy twice will be permanently removed from the class and awarded a failing grade.',1,22),"
    . "(21,'Lab Assignments','Lab assignments will be assigned approximately once a week, and will normally be due by the next class meeting.  Lab assignments typically require more time, patience, or insight than other homework assignments, because you are given the time to work on them in the lab, with your instructor there for help.  Lab assignments will also generally require a great deal more computer usage than other assignments, and so some students find it easier to finish in the lab rather than at home.',1,13),"
    . "(22,'Quizzes','Unannounced quizzes will be given at random intervals during the course.  Quizzes begin at the start of class; any student who is late to class therefore has less time to complete the quiz than students who arrive on time.  Quizzes are usually based on a recent homework assignment.  At no time during a quiz may a student confer any other people or any printed, written, or electronic materials except as expressly permitted by the professor or another exam proctor.  Any student in violation of this policy will receive a grade of 0 on the quiz.',1,16),"
    . "(23,'Online Discussion','Students are required to publish a new post to the course blog at least once per month.  (In Spring semesters, January and February are considered the same month.)  In order for posts to be worthy of course credit, they must:\n<ul><li>Pertain to course content in some way</li><li>Contain a <b>clickable</b> link to a news story on another site</li><li>Contain a brief summary, written by you, of the article</li></ul>\nSee <a href=\"http://www.matcmp.ncc.edu/cmp110blog/?p=16\">this sample post</a> for an idea of what\'s expected.\n\nStudents are also required to publish a comment to someone else\'s post at least once per month.  Any student who fails to meet these minimum posting and commenting requirements will <b>earn zero Online Discussion points for the course</b>.  Further details are available on the course blog site.',1,14),"
    . "(26,'Electronics','Electronic devices are prohibited in all class meetings.  Exceptions will be announced in advance as necessary.  Any student who owns, possesses, or is responsible for any device which causes a visual or auditory distraction will be removed from class and marked absent.  Any student who violates this policy twice will be permanently removed from the class and awarded a failing grade.',1,23),"
    . "(27,'Attendance','A student must be present at the start and at the end of class to be considered \"present\"; otherwise, the student will be marked \"absent\".  Planned absences may be excused if they are pre-approved.  A request for pre-approval must be made via e-mail, no later than 48 hours before the planned absence. No absence will be excused without a timely pre-approval request.  Supporting documentation of a pre-approved absence must be submitted within one week of the absence; otherwise, the absence will not be excused.  If it is not possible to deliver such documentation in person within one week, bring it to the Math Department office, and have a secretary time stamp it and deliver it to me.',1,24),"
    . "(28,'Twitter','',0,10)";

foreach( $tables as $name=>$create ) {
    
    // Make sure the table doesn't exist already
    $exists_query = "show tables like \"$name\"";
    $exists_result = $db->query( $exists_query );
    if( $exists_result->num_rows == 0 ) {
        
        // Create the table
        $db->query( $create );
        
        // Prepopulate data
        if( isset( $data[ $name ] ) )
            $db->query( $data[ $name ] );
    }
}

?>
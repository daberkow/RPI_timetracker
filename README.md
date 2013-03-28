RPI_timetracker
===============

Time Tracking

Parts that arent finished:
    Email reminders
    Direct send to payrole
    Delete Template
    Key setup to share info
    
Back Burner:
    Basic Version

Version 1.9 Table Update
    CREATE TABLE IF NOT EXISTS `email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `group` int(11) NOT NULL,
  `setting` tinyint(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;
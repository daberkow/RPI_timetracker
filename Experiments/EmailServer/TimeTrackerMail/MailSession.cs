using System;
using System.Net.Mail;
using System.Net;
using MySql.Data;
using MySql.Data.MySqlClient;

namespace TimeTrackerMail
{
	public class MailSession
	{
		public MailSession ()
		{
			string cs = @"server=localhost;userid=timetracker;password=DdCyzpALrxndc6BY;database=timetracker";

			MySqlConnection connect = null;
			MySqlDataReader reader = null;

			try {
				connect = new MySqlConnection (cs);
				connect.Open ();

				MySqlCommand cmd = new MySqlCommand();
				cmd.CommandText = "SELECT * FROM `email`";
		        cmd.Prepare();
		        cmd.ExecuteNonQuery();

				reader = cmd.ExecuteReader();

	            while (reader.Read()) 
	            {
	                //Console.WriteLine(reader.GetInt32(0) + ": " + reader.GetString(1));
	            }

			} catch (Exception ex) {
			} finally {
				if (connect != null)
				{
					connect.Close();
				}
			}
		}
	}
}


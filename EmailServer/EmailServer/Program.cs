﻿using System;
using System.Collections.Generic;
using System.Text;
using MySql.Data.MySqlClient;

/**
 *		Program	->GroupData(for group information) 
 * 			   	->UserStore	->GroupStore
 * 
 * 
 */

namespace EmailServer
{
    class Program
    {
        private static string MyConString = "SERVER=127.0.0.1;" +
                    "DATABASE=timetracker;" +
                    "UID=timetracker;" +
                    "PASSWORD=DdCyzpALrxndc6BY;";


        /**
         * Get email group settings, get users, get users who have no logs, check if user should get email, send
         * 
         * 
         */

        static void Main (string[] args)
		{
			List<List<string>> EmailJobs = getJobs ();// id of email table, groupid, userid, type, setting
			if (EmailJobs.Count == 1) {
				if (EmailJobs[0][0] == "Error"){
					Console.WriteLine("Error getting data");
					return;
				}
					
			}
			List<GroupData> GroupConverted = new List<GroupData>();
			for (int k = 0; k < EmailJobs.Count; k++) {
				if (findGroupindex(Int32.Parse(EmailJobs[k][1]), GroupConverted))
				{
					//dont add found
				}else{
					//Add info to groupData because no group data
					bool emailoverride = true;
					bool emailgroup = true;
					if (EmailJobs[k][4] == "1")
					{//if the job is overall standard
						if (EmailJobs[k][3] == "0"){
							emailgroup = false;
						}
					}else{
						//user allowed override
						if (EmailJobs[k][3] == "0"){
							emailoverride = false;
						}
					}
					GroupData tempGroup = new GroupData("",Int32.Parse(EmailJobs[k][1]),emailoverride,emailgroup);
					GroupConverted.Add(tempGroup);
				}
			}

			for (int k = 0; k < EmailJobs.Count; k++) {
				List<List<string>> Users = getUsers (EmailJobs[k][1]); //username, group name, group id
				for (int i = 0; i < Users.Count; i++) {
					//start converting to user stores
					 string Days = getDays("2");
				}
			}

           
            //SELECT DATEDIFF(NOW(),(SELECT `submitted` FROM timedata WHERE (((timedata.user)=2)) ORDER BY timedata.submitted DESC LIMIT 0,1));
            Console.Write("test");
        }

		public static bool findGroupindex (int passedID, List<GroupData> passedData)
		{
			//code this
			for (int i = 0; i < passedData.Count; i++) {
				if (passedData[i].intID == passedID)
					return true;
			}
			return false;
		}

        public static List<List<string>> getJobs()//type 2 allows users to have custom setting, type 1 is default for group
        {
            List<List<string>> return_data = new List<List<string>>();
            
            MySqlConnection connection = new MySqlConnection(MyConString);

            try
            {
                MySqlCommand command = connection.CreateCommand();
                MySqlDataReader Reader;
                command.CommandText = "SELECT * FROM `email`;";
                connection.Open();
                Reader = command.ExecuteReader();
                while (Reader.Read())
                {
                    List<string> row = new List<string>();
                    row.Add(Reader["id"].ToString());
                    row.Add(Reader["group"].ToString());
                    row.Add(Reader["user"].ToString());
                    row.Add(Reader["type"].ToString());
                    row.Add(Reader["setting"].ToString());
                    return_data.Add(row);
                }
                connection.Close();
                return return_data;
            }
            catch (Exception e)
            {
                try
                {
                    connection.Close();
                }
                catch { }
                List<string> row = new List<string>();
                row.Add("Error");
                return_data.Add(row);
                return return_data;
            }
        }
        public static List<List<string>> getUsers(string passedgroup)//type 2 allows users to have custom setting, type 1 is default for group
        {
            List<List<string>> return_data = new List<List<string>>();

            MySqlConnection connection = new MySqlConnection(MyConString);

            try
            {
                MySqlCommand command = connection.CreateCommand();
                MySqlDataReader Reader;
                command.CommandText = "SELECT users.username, groups.name, groupusers.groupid FROM (groupusers INNER JOIN users ON groupusers.userid = users.id) INNER JOIN groups ON groupusers.groupid = groups.id WHERE (((groupusers.privilege)>0) AND ((groupusers.groupid)='" + passedgroup + "'));";

                connection.Open();
                Reader = command.ExecuteReader();
                while (Reader.Read())
                {
                    List<string> row = new List<string>();
                    row.Add(Reader["username"].ToString());
                    row.Add(Reader["name"].ToString());
                    row.Add(Reader["groupid"].ToString());
                    return_data.Add(row);
                }
                connection.Close();
                return return_data;
            }
            catch (Exception e)
            {
                try
                {
                    connection.Close();
                }
                catch { }
                List<string> row = new List<string>();
                row.Add("Errror");
                return_data.Add(row);
                return return_data;
            }
        }
        public static string getDays(string passedUser)//type 2 allows users to have custom setting, type 1 is default for group
        {
            string return_data = "";

            MySqlConnection connection = new MySqlConnection(MyConString);

            try
            {
                MySqlCommand command = connection.CreateCommand();
                MySqlDataReader Reader;
                command.CommandText = "SELECT DATEDIFF(NOW(),(SELECT `submitted` FROM timedata WHERE (((timedata.user)='" + passedUser + "')) ORDER BY timedata.submitted DESC LIMIT 0,1)) AS diff;";

                connection.Open();
                Reader = command.ExecuteReader();
                while (Reader.Read())
                {
                    return_data = Reader["diff"].ToString();
                }
                connection.Close();
                return return_data;
            }
            catch (Exception e)
            {
                try
                {
                    connection.Close();
                }
                catch { }
                return_data = "Error";
                return return_data;
            }
        }
    
    }
}

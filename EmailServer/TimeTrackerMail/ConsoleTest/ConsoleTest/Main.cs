using System;
using System.Collections.Generic;
using MySql.Data.MySqlClient;

namespace ConsoleTest
{
	class MainClass
	{
		public static void Main (string[] args)
		{
			List<string> test = getJobs("timetracker", "DdCyzpALrxndc6BY", "timetracker");
			string test2 = "";
		}

		public static List<string> getJobs(string passed_username, string passed_password, string passed_db)
		{
			Console.WriteLine("Starting MYsql");
			List<String> return_data = new List<String>();
            string MyConString = "SERVER=localhost;" +
                    "DATABASE=" + passed_db + ";" +
                    "UID=" + passed_username + ";" +
                    "PASSWORD=" + passed_password + ";";
            MySqlConnection connection = new MySqlConnection(MyConString);

            try
            {
                MySqlCommand command = connection.CreateCommand();
                MySqlDataReader Reader;
                command.CommandText = "SELECT `user`, `group`, `type` FROM `email` WHERE `setting`=1";
                connection.Open();
                Reader = command.ExecuteReader();
                /*while (Reader.Read())
                {
					return_data.Add(Reader["user"].ToString());
					return_data.Add(Reader["group"].ToString());
					return_data.Add(Reader["type"].ToString());
                }*/
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
                return_data.Add("ERROR");
                return return_data;
            }
		}
	}
}

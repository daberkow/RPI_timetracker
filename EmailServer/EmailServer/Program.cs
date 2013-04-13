using System;
using System.Collections.Generic;
using System.Text;
using MySql.Data.MySqlClient;

namespace EmailServer
{
    class Program
    {
        private static string MyConString = "SERVER=192.168.182.130;" +
                    "DATABASE=timetracker;" +
                    "UID=timetracker;" +
                    "PASSWORD=DdCyzpALrxndc6BY;";



        /**
         * Get email group settings, get users, get users who have no logs, check if user should get email, send
         * 
         * 
         */

        static void Main(string[] args)
        {
            List<List<string>> EmailJobs = getJobs();
            List<List<string>> Users = getUsers("1");
            string Days = getDays("2");
            //SELECT DATEDIFF(NOW(),(SELECT `submitted` FROM timedata WHERE (((timedata.user)=2)) ORDER BY timedata.submitted DESC LIMIT 0,1));
            Console.Write("test");
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
                row.Add("Errror");
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

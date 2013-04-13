using System;
using System.Collections.Generic;

namespace EmailServer
{
	public class UserStore
	{
		public string intUsername = "";
		public int intID = -1;
		public List<GroupStore> intGroups = new List<GroupStore>();

		public UserStore ()
		{
		}

		public UserStore (string passedUsername, int passedID, List<GroupStore> passedGroups)
		{
			intUsername = passedUsername;
			intID = passedID;
			intGroups = passedGroups;
		}
	}
}


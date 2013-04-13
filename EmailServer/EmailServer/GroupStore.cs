using System;

namespace EmailServer
{
	public class GroupStore
	{
		public string intGroup = "";
		public int intID = -1;
		public int intDaysBetween = -1;

		public GroupStore ()
		{
		}

		public GroupStore (string passedGroup, int passedID, int passedDays)
		{
			intGroup = passedGroup;
			intID = passedID;
			intDaysBetween = passedDays;
		}
	}
}


using System;

namespace EmailServer
{
	public class GroupData
	{
		public string intGroup = "";
		public int intID = -1;
		public bool intAllowOverride = true;

		public GroupData ()
		{
		}

		public GroupData (string passedGroup, int passedID, bool passedOverride)
		{
			intGroup = passedGroup;
			intID = passedID;
			intAllowOverride = passedOverride;
		}
	}
}


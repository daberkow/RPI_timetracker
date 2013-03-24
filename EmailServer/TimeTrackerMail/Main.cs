using System;
using System.ServiceProcess;
using System.Configuration.Install;
using System.ComponentModel;

using System.Collections.Generic;
using System.Text;

namespace TimeTrackerMail
{
	class MainClass : ServiceBase
	{
		public static void Main (string[] args)
		{
			ServiceBase.Run(new MainClass());
		}

		public MainClass ()
		{
			BackgroundWorker Emailer = new BackgroundWorker();
			Emailer.WorkerSupportsCancellation = true;
            Emailer.DoWork += new DoWorkEventHandler(bw_DoWork);
		}

		private void bw_DoWork (object sender, DoWorkEventArgs e)
		{
			BackgroundWorker worker = sender as BackgroundWorker;
			if ((worker.CancellationPending == true))
            {
                e.Cancel = true;
                break;
            }


		}

			

		protected override void OnStart(string[] args)
		{
		    base.OnStart(args);
			MainClass();
		}

		protected override void OnStop()
		{
		    base.OnStop();
		}
	}

	[RunInstaller(true)]
	class TimeTrackerMailInstaller : ServiceInstaller
	{
		public TimeTrackerMailInstaller()
		{
			var processInstaller = new ServiceProcessInstaller();
		    var serviceInstaller = new ServiceInstaller();

		    //set the privileges

		    processInstaller.Account = ServiceAccount.LocalSystem;

		    serviceInstaller.DisplayName = "My Service";
		    serviceInstaller.StartType = ServiceStartMode.Manual;

		    //must be the same as what was set in Program's constructor
		    serviceInstaller.ServiceName = "My Service";

		    this.Installers.Add(processInstaller);
		    this.Installers.Add(serviceInstaller);
		}
	}
}

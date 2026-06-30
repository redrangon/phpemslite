const clock = function(userOptions)
{
	this.userOptions = userOptions;
	this.time = 0;
	this.h = 0;
	this.m = 0;
	this.s = 0;
	this.t = 0;
	this.interval = null;

	this.init = (leftTime)=>
	{
		if(!leftTime)leftTime = this.userOptions.leftTime;
		this.time = this.userOptions.time - leftTime;
		this.s = this.time % 60;
		this.m = parseInt(this.time%3600/60);
		this.h = parseInt(this.time/3600);
	}

	this.refresh = (leftTime) => {
		this.init(leftTime);
	}

	this.setval = ()=>
	{
		let h,m,s;
		s = this.s >= 10?this.s:'0'+this.s.toString();
		m = this.m >= 10?this.m:'0'+this.m.toString();
		h = this.h >= 10?this.h:'0'+this.h.toString();
		this.userOptions.show(h,m,s);
	}

	this.clear = ()=>
	{
		clearInterval(this.interval);
	}

	this.step = ()=>
	{
		if(this.s > 0)
		{
			this.s--;
		}
		else
		{
			if(this.m > 0)
			{
				this.m--;
				this.s = 60;
				this.s--;
			}
			else
			{
				if(this.h > 0)
				{
					this.h--;
					this.m = 60;
					this.m--;
					this.s = 60;
					this.s--;
				}
				else
				{
					this.clear();
					this.userOptions.finish();
					return ;
				}
			}
		}
		this.setval();
	}
	this.init();
	this.interval = setInterval(this.step, 1000);
};

export default clock;

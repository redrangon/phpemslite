import {encrypt,decrypt} from "@/framework/security";

const storage = function(token)
{
	this.token = token;
	this.question = {};
	this.storage = window.localStorage;
	this.addItem = function(name,value){
		let tmp = {};
		this.question[name] = value;
		tmp[this.token] = this.question;
		this.storage.setItem('questions',encrypt(tmp))
	}
	this.getItem = function(){
		return this.question;
	}
	this.syncData = function(data){
		let tmp = {};
		tmp[this.token] = data;
		this.storage.setItem('questions',encrypt(tmp))
	}
	this.initData = function(){
		let data;
		try{
			data = decrypt(this.storage.getItem('questions'));
		}catch(e){
			data = {};
		}
		let tmp = {};
		for(let x in data)
		{
			if(x === this.token)this.question = data[x];
			else delete data[x];
		}
		tmp[this.token] = this.question;
		this.storage.setItem('questions',encrypt(tmp))
	}
	this.initData();
}
export default storage;
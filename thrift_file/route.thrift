namespace php Route

// 请求的参数
struct Data {
    1: string siteid
    2: string request_uri
}

//返回的内容
struct resData {
	1:string id
	2:string siteid
	3:string param
	4:i8 type
	5:string mca
	6:string url
	7:string hash
	8:string hashid
	9:i32 addtime
	10:i32 uptime
}

// 提供的服务
service DataService {
	resData getRoute(1:Data data),
}


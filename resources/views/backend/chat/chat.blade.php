<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chatterbox</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.js"></script>
    <style>
        	body,html{
			height: 100%;
			margin: 0;
			background: #7F7FD5;
	       background: -webkit-linear-gradient(to right, #91EAE4, #86A8E7, #7F7FD5);
	        background: linear-gradient(to right, #91EAE4, #86A8E7, #7F7FD5);
		}

            .chat{
                margin-top: auto;
                margin-bottom: auto;
            }
            .card{
                height: 500px;
                border-radius: 15px !important;
                background-color: rgba(0,0,0,0.4) !important;
            }
            .contacts_body{
                padding:  0.75rem 0 !important;
                overflow-y: auto;
                white-space: nowrap;
            }
            .msg_card_body{
                overflow-y: auto;
            }
            .card-header{
                border-radius: 15px 15px 0 0 !important;
                border-bottom: 0 !important;
            }
        .card-footer{
            border-radius: 0 0 15px 15px !important;
                border-top: 0 !important;
        }
            .container{
                align-content: center;
            }
            .search{
                border-radius: 15px 0 0 15px !important;
                background-color: rgba(0,0,0,0.3) !important;
                border:0 !important;
                color:white !important;
            }
            .search:focus{
                box-shadow:none !important;
            outline:0px !important;
            }
            .type_msg{
                background-color: rgba(0,0,0,0.3) !important;
                border:0 !important;
                color:white !important;
                height: 60px !important;
                overflow-y: auto;
            }
                .type_msg:focus{
                box-shadow:none !important;
            outline:0px !important;
            }
            .attach_btn{
        border-radius: 15px 0 0 15px !important;
        background-color: rgba(0,0,0,0.3) !important;
                border:0 !important;
                color: white !important;
                cursor: pointer;
            }
            .send_btn{
        border-radius: 0 15px 15px 0 !important;
        background-color: rgba(0,0,0,0.3) !important;
                border:0 !important;
                color: white !important;
                cursor: pointer;
            }
            .search_btn{
                border-radius: 0 15px 15px 0 !important;
                background-color: rgba(0,0,0,0.3) !important;
                border:0 !important;
                color: white !important;
                cursor: pointer;
            }
            .contacts{
                list-style: none;
                padding: 0;
            }
            .contacts li{
                width: 100% !important;
                padding: 5px 10px;
                margin-bottom: 15px !important;
            }
        .active{
                background-color: rgba(0,0,0,0.3);
        }
            .user_img{
                height: 70px;
                width: 70px;
                border:1.5px solid #f5f6fa;

            }
            .user_img_msg{
                height: 40px;
                width: 40px;
                border:1.5px solid #f5f6fa;
                color: white;
            }
        .img_cont{
                position: relative;
                height: 70px;
                width: 70px;
        }
        .img_cont_msg{
                height: 40px;
                width: 40px;
        }
        .online_icon{
            position: absolute;
            height: 15px;
            width:15px;
            background-color: #4cd137;
            border-radius: 50%;
            bottom: 0.2em;
            right: 0.4em;
            border:1.5px solid white;
        }
        .offline{
            background-color: #c23616 !important;
        }
        .user_info{
            margin-top: auto;
            margin-bottom: auto;
            margin-left: 15px;
        }
        .user_info span{
            font-size: 20px;
            color: white;
        }
        .user_info p{
        font-size: 10px;
        color: rgba(255,255,255,0.6);
        }
        .video_cam{
            margin-left: 50px;
            margin-top: 5px;
        }
        .video_cam span{
            color: white;
            font-size: 20px;
            cursor: pointer;
            margin-right: 20px;
        }
        .msg_cotainer{
            margin-top: auto;
            margin-bottom: auto;
            margin-left: 10px;
            border-radius: 25px;
            background-color: #82ccdd;
            padding: 10px;
            /* position: relative; */
        }
        .msg_cotainer_send{
            margin-top: auto;
            margin-bottom: auto;
            margin-right: 10px;
            border-radius: 25px;
            background-color: #78e08f;
            padding: 10px;
            /* position: relative; */
        }
        .msg_time{
            /* position: absolute; */
            left: 0;
            bottom: -15px;
            color: rgba(255,255,255,0.5);
            font-size: 10px;
        }
        .msg_time_send{
            /* position: absolute; */
            right:0;
            bottom: -15px;
            color: rgba(255,255,255,0.5);
            font-size: 10px;
        }
        .msg_head{
            position: relative;
        }
        #action_menu_btn{
            position: absolute;
            right: 10px;
            top: 10px;
            color: white;
            cursor: pointer;
            font-size: 20px;
        }
        .action_menu{
            z-index: 1;
            position: absolute;
            padding: 15px 0;
            background-color: rgba(0,0,0,0.5);
            color: white;
            border-radius: 15px;
            top: 30px;
            right: 15px;
            display: none;
        }
        .action_menu ul{
            list-style: none;
            padding: 0;
        margin: 0;
        }
        .action_menu ul li{
            width: 100%;
            padding: 10px 15px;
            margin-bottom: 5px;
        }
        .action_menu ul li i{
            padding-right: 10px;

        }
        .action_menu ul li:hover{
            cursor: pointer;
            background-color: rgba(0,0,0,0.2);
        }
        .user_name {
            height: 40px;
            width: 40px;
            background-color: red;
        }
        @media(max-width: 576px){
        .contacts_card{
            margin-bottom: 15px !important;
        }
        }

        #top {
            padding-top: 40px;
            padding-bottom: 40px;
            padding-left: 40px;
            padding-bottom: 40px;
        }

        .a {
            padding: 20px;
            background-color: white;
            border-radius: 20px;
            text-decoration: none;
        }
        .a:hover{
            background-color: gray;
        }
    </style>
  </head>
  <body>
    <div id="chat">
        <div id="top">
            <div >
                <a href="https://greencard-sa.com/admin/" class="a">Go to panel</a>

            </div>
            <br/><h4>ISSUE : {{ $name_sub }}</h4>
        </div>
        <div class="container-fluid h-100">
			<div class="row justify-content-center h-100">
				<div class="col-md-4 col-xl-3 chat"><div class="card mb-sm-3 mb-md-0 contacts_card">
					<div class="card-header">
						<!-- <div class="input-group">
							<input type="text" placeholder="Search..." name="" class="form-control search">
							<div class="input-group-prepend">
								<span class="input-group-text search_btn"><i class="fas fa-search"></i></span>
							</div>
						</div> -->
					</div>
					<div class="card-body contacts_body">
						<ui class="contacts" id="contacts">

						</ui>
					</div>
					<div class="card-footer"></div>
				</div></div>
				<div class="col-md-8 col-xl-6 chat">
					<div class="card">
						<div class="card-header msg_head">
							<div class="d-flex bd-highlight">
								<!-- <div class="img_cont">
									<img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img">

								</div> -->
								<div class="user_info">
									<span id="userName"></span>
								</div>
							</div>
						</div>
						<div id="chatContainer" class="card-body msg_card_body">

						</div>
						<div class="card-footer">
							<div class="input-group">
								<div class="input-group-append">
									<span class="input-group-text attach_btn"></span>
								</div>
								<textarea id="chat-txt" name="" class="form-control type_msg" placeholder="Type your message..."></textarea>
								<div class="input-group-append" onclick="postChat()">
									<span class="input-group-text send_btn"><i class="fas fa-location-arrow"></i></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

    </div>
    <script src="https://www.gstatic.com/firebasejs/8.2.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.2.1/firebase-database.js"></script>
    <!-- <script src="script.js"></script>    -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script>
        $(document).ready(function(){
            $('#action_menu_btn').click(function(){
                $('.action_menu').toggle();
            });
        });
        const firebaseConfig = {
            apiKey: "AIzaSyAPd9KicGGk9r-GVxZl-AzkTyhEzGraKfU",
            authDomain: "greencrad-sa.firebaseapp.com",
            projectId: "greencrad-sa",
            storageBucket: "greencrad-sa.appspot.com",
            messagingSenderId: "429851791477",
            appId: "1:429851791477:web:72d7685bae2bd55814a83c",
        };
        firebase.initializeApp(firebaseConfig);
        const db = firebase.database();

        // const username = prompt("What's your name?")
        var selected_user = 0;
        const subject = {{ $subject }};

        function postChat() {

            const user = {
                _id: 1,
                name: "Support",
                subject: subject,
                subjectName: "{{ $name_sub }}",
                to: selected_user
            }
            const timestamp = Date.now();
            const chatTxt = document.getElementById("chat-txt");
            const message = chatTxt.value;
            chatTxt.value = "";

            const msg = {
                text: message,
                timestamp: timestamp,
                user: user
            }
            if(message.length && selected_user != 0){
                db.ref("chat/").push(msg);
            }

        }

        // function getUsers(){
        //     const data = db.ref("chat/");
        // }

        const listUsers = [];


        function setUser(id,name){

            selected_user = id;

            var class_name='active';
            elements=document.getElementsByClassName(class_name)

            for(element of elements){
            element.classList.remove(class_name)
            }
            const li = document.getElementById(id);
            li.classList.add("active");
            document.getElementById("userName").innerText=name+" | USER ID #"+id;
            getMessages(id);
        }

        const fetchChat = db.ref("chat/");

        getUsers();

        let listMessages = [];

        function getMessages(id){
            listMessages = []
            fetchChat.on("child_added", function (snapshot) {
                const messages = snapshot.val();
                var msg = "";
                var newMsg = false;
                console.log("new msg", messages);
                if(subject == messages.user.subject){
                    if(messages.user._id == 1 && messages.user.to == selected_user){
                        msg = `
                            <div class="d-flex justify-content-end mb-4">
                                <div class="msg_cotainer_send">
                                    ${ messages.text}
                                    <span class="msg_time_send">${moment(messages.timestamp).startOf('hour').fromNow()}</span>
                                </div>
                                <!-- <div class="img_cont_msg">
                                    <img src="" class="rounded-circle user_img_msg">
                                    <span class="user_name"></span>
                                </div>-->
                            </div>
                        `;
                        listMessages.push(msg);
                    }else{
                        if(id == messages.user._id){
                            existUser = listUsers.findIndex(u => u.id == messages.user._id);
                            if(existUser == -1){
                                if(subject == messages.user.subject){
                                    listUsers.push({
                                        id: messages.user._id,
                                        name: messages.user.name
                                    })
                                }

                            }
                            msg = `
                                <div class="d-flex justify-content-start mb-4">
                                    <!-- <div class="img_cont_msg">
                                        <img src="" class="rounded-circle user_img_msg">
                                        <span class="rounded-circle user_img_msg"></span>
                                    </div>-->
                                    <div class="msg_cotainer">
                                        ${ messages.text}
                                        <span class="msg_time">${moment(messages.timestamp).startOf('hour').fromNow()}</span>
                                    </div>
                                </div>
                            `;
                            listMessages.push(msg);
                        }else{
                            newMsg = true
                        }

                    }
                }

                document.getElementById("chatContainer").innerHTML = listMessages;
                var objDiv = document.getElementById("chatContainer");
                objDiv.scrollTop = objDiv.scrollHeight;

                const contacts = listUsers.map(u => {
                    const classLi = u.id == selected_user ? "active" : "";
                    const user_html = `
                        <li id="${u.id}" class="${classLi}" onclick="setUser(${u.id},'${u.name}')">
                            <div class="d-flex bd-highlight">

                                <div class="user_info">
                                    <span>${u.name}</span>

                                </div>
                            </div>
                        </li>
                    `;
                    // ${newMsg ? "<p>New Messages</p>": ""}
                    return user_html

                })

                document.getElementById("contacts").innerHTML = contacts;


            });
        }

        function getUsers(){
            fetchChat.on("child_added", function (snapshot) {
                const messages = snapshot.val();

                if(messages.user._id == 1){

                }else{
                    existUser = listUsers.findIndex(u => u.id == messages.user._id);
                    if(existUser == -1){
                        if(subject == messages.user.subject){
                            console.log("user : ", messages.user);

                            listUsers.push({
                                id: messages.user._id,
                                name: messages.user.name
                            })
                        }

                    }
                }


                const contacts = listUsers.map(u => {
                    const classLi = u.id == selected_user ? "active" : "";
                    const user_html = `
                        <li id="${u.id}" class="${classLi}" onclick="setUser(${u.id},'${u.name}')">
                            <div class="d-flex bd-highlight">

                                <div class="user_info">
                                    <span>${u.name}</span>
                                    <!--<p>Kalid is online</p>-->
                                </div>
                            </div>
                        </li>
                    `;
                    return user_html

                })

                document.getElementById("contacts").innerHTML = contacts;

            });
        }

    </script>
  </body>
</html>

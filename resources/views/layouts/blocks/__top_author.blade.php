<div class="top_author">
    <h4 class="sidebar-title size13 bold"><i class="iconfont ic-recommend"></i> TOP Thành viên <small class="pull-right"><a href="{{ route('user.members') }}" target="_blank">Xem tất cả</a></small></h4>
    @foreach($top_users as $index => $author)
        <div class="author_item minh50">
            <div class="avatar pull-left">
                @if($index == 0)
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="30px" height="30px" viewBox="0 0 94.5 94.5" style="enable-background:new 0 0 94.5 94.5;" xml:space="preserve">
                    <g>
                    	<g id="Layer_1_17_">
                    		<g>
                    			<g>
                    				<g>
                    					<path d="M84.762,41.232c-4.92,3.529-11.826,8.222-14.941,8.222c-0.617,0-0.815-0.186-0.959-0.361       c-1.272-1.568-0.928-7.066,1.025-16.335c0.193-0.918-0.291-1.841-1.152-2.204c-0.861-0.365-1.861-0.066-2.383,0.71       c-6.721,10.021-9.799,12.123-11.194,12.123c-1.979,0-3.99-5.692-5.986-16.92c-0.159-0.896-0.915-1.549-1.813-1.601v-0.011       c-0.019,0-0.037,0.004-0.056,0.004c-0.019,0-0.04-0.004-0.058-0.004v0.011c-0.895,0.052-1.649,0.705-1.811,1.601       c-1.998,11.228-4.011,16.92-5.988,16.92c-1.396,0-4.472-2.103-11.192-12.123c-0.521-0.776-1.521-1.075-2.384-0.71       c-0.862,0.363-1.346,1.286-1.152,2.204c1.952,9.269,2.299,14.767,1.024,16.335c-0.141,0.176-0.342,0.361-0.958,0.361       c-3.115,0-10.021-4.692-14.943-8.222c-0.778-0.559-1.845-0.468-2.52,0.209c-0.676,0.674-0.765,1.741-0.207,2.52       c11.253,14.392,9.135,30.91,9.135,30.91c-0.041,0.229-0.063,0.463-0.063,0.709c0,4.058,5.682,5.664,10.631,6.61       c5.453,1.045,12.635,1.627,20.266,1.647v0.002c0.073,0,0.147,0,0.22-0.002c0.073,0.002,0.146,0.002,0.221,0.002v-0.002       c7.629-0.021,14.812-0.603,20.263-1.647c4.95-0.946,10.633-2.555,10.633-6.61c0-0.246-0.022-0.479-0.063-0.709       c0,0-2.117-16.521,9.134-30.91c0.559-0.778,0.471-1.846-0.205-2.52C86.607,40.766,85.542,40.674,84.762,41.232z M47.522,78.238       h-0.015c-0.07,0-0.137,0.003-0.206,0.003c-0.07,0-0.135-0.003-0.205-0.003h-0.015c-12.127-0.035-20.144-1.446-23.682-2.657       c3.538-1.213,11.555-2.624,23.682-2.658h0.029c0.063,0,0.126,0,0.19,0s0.124,0,0.19,0h0.03       c12.126,0.034,20.142,1.445,23.681,2.658C67.664,76.792,59.648,78.203,47.522,78.238z" fill="#FCCA00"/>
                    				</g>
                    			</g>
                    			<g>
                    				<g>
                    					<circle cx="47.302" cy="16.573" r="5.914" fill="#FCCA00"/>
                    				</g>
                    			</g>
                    			<g>
                    				<g>
                    					<circle cx="22.339" cy="23.718" r="4.928" fill="#FCCA00"/>
                    				</g>
                    			</g>
                    			<g>
                    				<g>
                    					<circle cx="71.855" cy="23.718" r="4.928" fill="#FCCA00"/>
                    				</g>
                    			</g>
                    			<g>
                    				<g>
                    					<circle cx="3.322" cy="37.759" r="3.322" fill="#FCCA00"/>
                    				</g>
                    			</g>
                    			<g>
                    				<g>
                    					<circle cx="91.178" cy="37.759" r="3.322" fill="#FCCA00"/>
                    				</g>
                    			</g>
                    		</g>
                    	</g>
                    </g>
                    </svg>
                    <a href="{{ route('user.index', ['slug' => $author->slug]) }}" class="avatar-circle">
                        <img src="{{ url('image/36/36/' . $author->avatar) }}" alt="{{ $author->name }}" title="{{ $author->name }}" class="img-circle">
                    </a>
                @elseif($index == 1)
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="30px" height="30px" viewBox="0 0 94.5 94.5" style="enable-background:new 0 0 94.5 94.5;" xml:space="preserve">
                    <g>
                        <g id="Layer_1_17_">
                            <g>
                                <g>
                                    <g>
                                        <path d="M84.762,41.232c-4.92,3.529-11.826,8.222-14.941,8.222c-0.617,0-0.815-0.186-0.959-0.361       c-1.272-1.568-0.928-7.066,1.025-16.335c0.193-0.918-0.291-1.841-1.152-2.204c-0.861-0.365-1.861-0.066-2.383,0.71       c-6.721,10.021-9.799,12.123-11.194,12.123c-1.979,0-3.99-5.692-5.986-16.92c-0.159-0.896-0.915-1.549-1.813-1.601v-0.011       c-0.019,0-0.037,0.004-0.056,0.004c-0.019,0-0.04-0.004-0.058-0.004v0.011c-0.895,0.052-1.649,0.705-1.811,1.601       c-1.998,11.228-4.011,16.92-5.988,16.92c-1.396,0-4.472-2.103-11.192-12.123c-0.521-0.776-1.521-1.075-2.384-0.71       c-0.862,0.363-1.346,1.286-1.152,2.204c1.952,9.269,2.299,14.767,1.024,16.335c-0.141,0.176-0.342,0.361-0.958,0.361       c-3.115,0-10.021-4.692-14.943-8.222c-0.778-0.559-1.845-0.468-2.52,0.209c-0.676,0.674-0.765,1.741-0.207,2.52       c11.253,14.392,9.135,30.91,9.135,30.91c-0.041,0.229-0.063,0.463-0.063,0.709c0,4.058,5.682,5.664,10.631,6.61       c5.453,1.045,12.635,1.627,20.266,1.647v0.002c0.073,0,0.147,0,0.22-0.002c0.073,0.002,0.146,0.002,0.221,0.002v-0.002       c7.629-0.021,14.812-0.603,20.263-1.647c4.95-0.946,10.633-2.555,10.633-6.61c0-0.246-0.022-0.479-0.063-0.709       c0,0-2.117-16.521,9.134-30.91c0.559-0.778,0.471-1.846-0.205-2.52C86.607,40.766,85.542,40.674,84.762,41.232z M47.522,78.238       h-0.015c-0.07,0-0.137,0.003-0.206,0.003c-0.07,0-0.135-0.003-0.205-0.003h-0.015c-12.127-0.035-20.144-1.446-23.682-2.657       c3.538-1.213,11.555-2.624,23.682-2.658h0.029c0.063,0,0.126,0,0.19,0s0.124,0,0.19,0h0.03       c12.126,0.034,20.142,1.445,23.681,2.658C67.664,76.792,59.648,78.203,47.522,78.238z" fill="#c9c9c9"/>
                                    </g>
                                </g>
                                <g>
                                    <g>
                                        <circle cx="47.302" cy="16.573" r="5.914" fill="#c9c9c9"/>
                                    </g>
                                </g>
                                <g>
                                    <g>
                                        <circle cx="22.339" cy="23.718" r="4.928" fill="#c9c9c9"/>
                                    </g>
                                </g>
                                <g>
                                    <g>
                                        <circle cx="71.855" cy="23.718" r="4.928" fill="#c9c9c9"/>
                                    </g>
                                </g>
                                <g>
                                    <g>
                                        <circle cx="3.322" cy="37.759" r="3.322" fill="#c9c9c9"/>
                                    </g>
                                </g>
                                <g>
                                    <g>
                                        <circle cx="91.178" cy="37.759" r="3.322" fill="#c9c9c9"/>
                                    </g>
                                </g>
                            </g>
                        </g>
                    </g>
                    </svg>
                    <a href="{{ route('user.index', ['slug' => $author->slug]) }}" class="avatar-circle">
                        <img src="{{ url('image/36/36/' . $author->avatar) }}" alt="{{ $author->name }}" title="{{ $author->name }}" class="img-circle">
                    </a>
                @elseif($index == 2)
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="30px" height="30px" viewBox="0 0 94.5 94.5" style="enable-background:new 0 0 94.5 94.5;" xml:space="preserve">
                    <g>
                        <g id="Layer_1_17_">
                            <g>
                                <g>
                                    <g>
                                        <path d="M84.762,41.232c-4.92,3.529-11.826,8.222-14.941,8.222c-0.617,0-0.815-0.186-0.959-0.361       c-1.272-1.568-0.928-7.066,1.025-16.335c0.193-0.918-0.291-1.841-1.152-2.204c-0.861-0.365-1.861-0.066-2.383,0.71       c-6.721,10.021-9.799,12.123-11.194,12.123c-1.979,0-3.99-5.692-5.986-16.92c-0.159-0.896-0.915-1.549-1.813-1.601v-0.011       c-0.019,0-0.037,0.004-0.056,0.004c-0.019,0-0.04-0.004-0.058-0.004v0.011c-0.895,0.052-1.649,0.705-1.811,1.601       c-1.998,11.228-4.011,16.92-5.988,16.92c-1.396,0-4.472-2.103-11.192-12.123c-0.521-0.776-1.521-1.075-2.384-0.71       c-0.862,0.363-1.346,1.286-1.152,2.204c1.952,9.269,2.299,14.767,1.024,16.335c-0.141,0.176-0.342,0.361-0.958,0.361       c-3.115,0-10.021-4.692-14.943-8.222c-0.778-0.559-1.845-0.468-2.52,0.209c-0.676,0.674-0.765,1.741-0.207,2.52       c11.253,14.392,9.135,30.91,9.135,30.91c-0.041,0.229-0.063,0.463-0.063,0.709c0,4.058,5.682,5.664,10.631,6.61       c5.453,1.045,12.635,1.627,20.266,1.647v0.002c0.073,0,0.147,0,0.22-0.002c0.073,0.002,0.146,0.002,0.221,0.002v-0.002       c7.629-0.021,14.812-0.603,20.263-1.647c4.95-0.946,10.633-2.555,10.633-6.61c0-0.246-0.022-0.479-0.063-0.709       c0,0-2.117-16.521,9.134-30.91c0.559-0.778,0.471-1.846-0.205-2.52C86.607,40.766,85.542,40.674,84.762,41.232z M47.522,78.238       h-0.015c-0.07,0-0.137,0.003-0.206,0.003c-0.07,0-0.135-0.003-0.205-0.003h-0.015c-12.127-0.035-20.144-1.446-23.682-2.657       c3.538-1.213,11.555-2.624,23.682-2.658h0.029c0.063,0,0.126,0,0.19,0s0.124,0,0.19,0h0.03       c12.126,0.034,20.142,1.445,23.681,2.658C67.664,76.792,59.648,78.203,47.522,78.238z" fill="#B79300"/>
                                    </g>
                                </g>
                                <g>
                                    <g>
                                        <circle cx="47.302" cy="16.573" r="5.914" fill="#B79300"/>
                                    </g>
                                </g>
                                <g>
                                    <g>
                                        <circle cx="22.339" cy="23.718" r="4.928" fill="#B79300"/>
                                    </g>
                                </g>
                                <g>
                                    <g>
                                        <circle cx="71.855" cy="23.718" r="4.928" fill="#B79300"/>
                                    </g>
                                </g>
                                <g>
                                    <g>
                                        <circle cx="3.322" cy="37.759" r="3.322" fill="#B79300"/>
                                    </g>
                                </g>
                                <g>
                                    <g>
                                        <circle cx="91.178" cy="37.759" r="3.322" fill="#B79300"/>
                                    </g>
                                </g>
                            </g>
                        </g>
                    </g>
                    </svg>
                    <a href="{{ route('user.index', ['slug' => $author->slug]) }}" class="avatar-circle">
                        <img src="{{ url('image/36/36/' . $author->avatar) }}" alt="{{ $author->name }}" title="{{ $author->name }}" class="img-circle">
                    </a>
                @else
                    <a href="{{ route('user.index', ['slug' => $author->slug]) }}" class="avatar-circle no-rank">
                        <img src="{{ url('image/36/36/' . $author->avatar) }}" alt="{{ $author->name }}" title="{{ $author->name }}" class="img-circle">
                    </a>
                @endif
            </div>
            <div class="body ml50">
                <div class="name bold pull-left">
                    <a href="{{ route('user.index', ['slug' => $author->slug]) }}">
                        {{ $author->name }}
                    </a>
                </div>
                <div class="point pull-right">
                    <div class="flexbox">
                        {{ $author->articles_count }}
                        <div class="ml5" data-tooltip="CCoin" data-placement="top">
                            <svg version="1.1" id="coin" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve" width="14px" height="14px"><path d="M256,0l-90,256l90,256c141.16,0,256-114.841,256-256S397.16,0,256,0z" style="fill: rgb(234, 111, 90);"></path> <path d="M0,256c0,141.159,114.841,256,256,256V0C114.841,0,0,114.841,0,256z" style="fill: rgb(239, 163, 122);"></path> <path d="M256,45l-21.531,16.148L256,75c99.804,0,181,81.196,181,181s-81.196,181-181,181l-21.531,13.852
                                	L256,467c116.346,0,211-94.654,211-211S372.346,45,256,45z" style="fill: rgb(248, 221, 217);"></path> <path d="M45,256c0,116.346,94.654,211,211,211v-30c-99.804,0-181-81.196-181-181S156.196,75,256,75V45
                                	C139.654,45,45,139.654,45,256z" style="fill: rgb(255, 255, 255);"></path> <g><path d="M303.853,218.255L350.884,181c-22.181-28.003-56.469-46-94.884-46l-20,30l20,30
                                		C274.704,195,292.146,203.476,303.853,218.255z" style="fill: rgb(253, 225, 210);"></path> <path d="M256,317l-20,30l20,30c38.415,0,72.703-17.997,94.884-46l-47.032-37.255
                                		C292.146,308.524,274.704,317,256,317z" style="fill: rgb(253, 230, 210);"></path></g> <path d="M195,256c0-33.636,27.364-61,61-61v-60c-66.72,0-121,54.28-121,121s54.28,121,121,121v-60
                                	C222.364,317,195,289.636,195,256z" style="fill: rgb(255, 255, 255);"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

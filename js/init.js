new Vue({
	template: `
		<div class="list-box">
			<div v-for="(curType, typeIndex) in p_list">
				<div style="padding-left: 20px;" @click="pullTypePage(curType.id, curType, typeIndex)" :class="[curType.open ? 'active' : '' ,'list_item']">{{ curType.name }}</div>
				<div v-if="curType.open" style="padding-left: 20px;">
					<div v-for="(curPage, pageIndex) in curType.list">
						<div style="padding-left: 20px;" @click="pullMusicList(typeIndex, pageIndex, curPage.id, curType.id)" :class="[curPage.open ? 'active' : '' ,'list_item']">{{ curPage.name }}</div>
						<div v-if="curPage.open">
							<div style="padding-left: 30px;" v-for="curMusic in curPage.list" :title="curMusic.name" @click="setPlayerUrl(curMusic)" class="list_item text-ellipsis">{{ curMusic.name }}</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	`,
	el: "#player-list",
	data: function(){
		return {
			baseUrl: "./dj.php",
			p_list: []
		}
	},
	methods: {
		// 设置播放源
		setPlayerUrl(curMusic){
			this.pullData(
				`type=get_audio&id=${curMusic.id}`,
				this.baseUrl,
				function(data){
					window.reset_player({
						"name": curMusic.name,
						"url": data.url
					});
				}
			)

		},
		// 获取一级分类
		pullTypeList(){
			let This = this;
			this.pullData(
				"type=get_tag",
				this.baseUrl,
				function(data){
					This.p_list = data.data;
				}
			)
		},
		// 获取分类分页
		pullTypePage(typeId, curType, typeIndex){

			let This = this;
			// 是否加载完成了已经列表
			if(curType.load){
				curType.open = !curType.open;
				return
			}
			this.pullData(
				`type=tag_page&id=${typeId}`,
				this.baseUrl,
				function(data){
					This.$set(This.p_list[typeIndex], 'load', true);
					This.$set(This.p_list[typeIndex], 'open', true);
					This.$set(This.p_list[typeIndex], 'list', data.data);
				}
			)
		},
		// 获取当前分页下的所有音乐
		pullMusicList(typeIndex, pageIndex, pageId, typeId){

			let This = this;
			// 是否加载完成了已经列表
			if(this.p_list[typeIndex].list[pageIndex].load){
				this.p_list[typeIndex].list[pageIndex].open = !this.p_list[typeIndex].list[pageIndex].open;
				return
			}
			this.pullData(
				`type=tag_list&page=${pageId}&id=${typeId}`,
				this.baseUrl,
				function(data){
					This.$set(This.p_list[typeIndex].list[pageIndex], 'load', true);
					This.$set(This.p_list[typeIndex].list[pageIndex], 'open', true);
					This.$set(This.p_list[typeIndex].list[pageIndex], 'list', data.data);
				}
			)
		},
		pullData(queryStr, url, cb){
			$.ajax({
			    type: 'GET',
			    async: true,
			    url : url,
			    data : queryStr,
			    dataType : 'json',
			    success : function(data){
			    	cb(data)
			    }
			})
		}
	},
	mounted(){
		this.pullTypeList();
	}
})
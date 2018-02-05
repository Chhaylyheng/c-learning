<div class="info-box mt0">

<ul class="PointMenu">

<li>
	<h2>
		<span class="attention  attn-emp font-size-80">New!</span> <?php echo __(':year年:month月 更新分',array('year'=>2017,'month'=>12)); ?>
	</h2>

	<h3><a href="<?php echo Asset::get_file('new_function_171218.pdf', 'docs'); ?>" target="_blank"><?php echo __(':date リリース',array('date'=>'2017/12/18')); ?> <i class="fa fa-file-pdf-o mr0"></i></a></h3>
	<p>
		<?php echo __('【小テスト】ドリルへの問題コピー'); ?><br>
		<?php echo __('【ドリル】小テストへの問題コピー'); ?><br>
		<?php echo __('【協働板】個人宛の返信 記事検索'); ?><br>
	</p>

	<h3><a href="<?php echo Asset::get_file('new_function_171204.pdf', 'docs'); ?>" target="_blank"><?php echo __(':date リリース',array('date'=>'2017/12/04')); ?> <i class="fa fa-file-pdf-o mr0"></i></a></h3>
	<p>
		<?php echo __('【バッジ】連絡・相談、協働板の未読数を表示'); ?><br>
		<?php echo __('【アンケート】アンケートを横断した提出一覧,学生の回答内容一覧'); ?><br>
		<?php echo __('【小テスト】小テストを横断した提出一覧,学生の解答内容一覧'); ?><br>
		<?php echo __('【レポート】レポートを横断した提出一覧'); ?><br>
	</p>
</li>

</ul>

<ol class="ManualMenu">

<li>
	<h2>
		<a href="<?php echo Asset::get_file('cl_manual_teacher_Basic.pdf', 'docs'); ?>" target="_blank">
			<?php echo __('基本操作') ?> <i class="fa fa-file-pdf-o mr0"></i>
		</a>
	</h2>
	<p>
		<?php echo __('講義の作成、編集、終了の仕方'); ?><br>
		<?php echo __('アカウント情報の変更'); ?><br>
		<?php echo __('副担当の設定'); ?><br>
	</p>
</li>

<li>
	<h2>
		<a href="<?php echo Asset::get_file('cl_manual_teacher_StudentManagement.pdf', 'docs'); ?>" target="_blank">
			<?php echo __('学生管理') ?> <i class="fa fa-file-pdf-o mr0"></i>
		</a>
	</h2>
	<p>
		<?php echo __('学生から講義に履修させる方法'); ?><br>
		<?php echo __('学生から初回ログインで取得する情報の設定'); ?><br>
	</p>
</li>

<li>
	<h2>
		<a href="<?php echo Asset::get_file('cl_manual_teacher_AttendanceManagement.pdf', 'docs'); ?>" target="_blank">
			<?php echo __('出席管理') ?> <i class="fa fa-file-pdf-o mr0"></i>
		</a>
	</h2>
	<p>
		<?php echo __('出席項目の設定、出席（予約出席、いますぐ出席）、出席状況の確認、CSV保存'); ?><br>
	</p>
</li>

<li>
	<h2>
		<a href="<?php echo Asset::get_file('cl_manual_teacher_Questionnaire.pdf', 'docs'); ?>" target="_blank">
			<?php echo __('アンケート') ?> <i class="fa fa-file-pdf-o mr0"></i>
		</a>
	</h2>
	<p>
		<?php echo __('クイックアンケート、通常アンケート'); ?><br>
		<?php echo __('ゲスト回答、先生による回答、匿名・記名設定'); ?><br>
		<?php echo __('公開・締切設定、CSVによる設問登録、CSV保存'); ?><br>
	</p>
</li>

<li>
	<h2>
		<a href="<?php echo Asset::get_file('cl_manual_teacher_Quiz.pdf', 'docs'); ?>" target="_blank">
			<?php echo __('小テスト') ?> <i class="fa fa-file-pdf-o mr0"></i>
		</a>
	</h2>
	<p>
		<?php echo __('作成、公開、解答、締切'); ?><br>
		<?php echo __('解答状況の確認、提出状況の確認'); ?><br>
	</p>
</li>

<li>
	<h2>
		<a href="<?php echo Asset::get_file('cl_manual_teacher_Drill.pdf', 'docs'); ?>" target="_blank">
			<?php echo __('ドリル') ?> <i class="fa fa-file-pdf-o mr0"></i>
		</a>
	</h2>
	<p>
		<?php echo __('カテゴリ作成、ドリル作成、公開、解答'); ?><br>
		<?php echo __('正答状況の確認、実施状況の確認、問題分析'); ?><br>
	</p>
</li>

<li>
	<h2>
		<a href="<?php echo Asset::get_file('cl_manual_teacher_Materials.pdf', 'docs'); ?>" target="_blank">
			<?php echo __('教材倉庫') ?> <i class="fa fa-file-pdf-o mr0"></i>
		</a>
	</h2>
	<p>
		<?php echo __('カテゴリ作成、教材（文書、画像、動画等）登録、公開、閲覧'); ?><br>
		<?php echo __('既読者の確認'); ?><br>
		<?php echo __('アンケートや小テストなどコンテンツ差込み'); ?><br>
	</p>
</li>

<li>
	<h2>
		<a href="<?php echo Asset::get_file('cl_manual_teacher_Forum.pdf', 'docs'); ?>" target="_blank">
			<?php echo __('協働板') ?> <i class="fa fa-file-pdf-o mr0"></i>
		</a>
	</h2>
	<p>
		<?php echo __('協働板作成（対象学生選択）、スレッド作成、コメント作成、表示順序変更'); ?><br>
		<?php echo __('タイル表示'); ?><br>
	</p>
</li>

<li>
	<h2>
		<a href="<?php echo Asset::get_file('cl_manual_teacher_Report.pdf', 'docs'); ?>" target="_blank">
			<?php echo __('レポート') ?> <i class="fa fa-file-pdf-o mr0"></i>
		</a>
	</h2>
	<p>
		<?php echo __('テーマの新規作成、公開、締切'); ?><br>
		<?php echo __('レポートの評価（先生からの評価、コメント、学生同士の相互評価）'); ?><br>
		<?php echo __('評価の公開'); ?><br>
	</p>
</li>

<li>
	<h2>
		<a href="<?php echo Asset::get_file('cl_manual_teacher_ActivityLog.pdf', 'docs'); ?>" target="_blank">
			<?php echo __('活動履歴') ?> <i class="fa fa-file-pdf-o mr0"></i>
		</a>
	</h2>
	<p>
		<?php echo __('テーマの新規作成、公開、確認'); ?><br>
	</p>
</li>

<li>
	<h2>
		<a href="<?php echo Asset::get_file('cl_manual_teacher_Contact.pdf', 'docs'); ?>" target="_blank">
			<?php echo __('連絡・相談') ?> <i class="fa fa-file-pdf-o mr0"></i>
		</a>
	</h2>
	<p>
		<?php echo __('学生への連絡（個別、選択、一斉）、履歴確認'); ?><br>
		<?php echo __('学生からの相談、返信'); ?><br>
	</p>
</li>

<li>
	<h2>
		<a href="<?php echo Asset::get_file('cl_manual_teacher_News.pdf', 'docs'); ?>" target="_blank">
			<?php echo __('ニュース') ?> <i class="fa fa-file-pdf-o mr0"></i>
		</a>
	</h2>
	<p>
		<?php echo __('ニュースの追加、編集、終了、削除'); ?><br>
		<?php echo __('アンケートや小テスト、教材倉庫，協働板などコンテンツ差込み'); ?><br>
	</p>
</li>

<li>
	<h2>
		<a href="<?php echo Asset::get_file('cl_manual_teacher_PurchaseProcedure.pdf', 'docs'); ?>" target="_blank">
			<?php echo __('購入手続き') ?> <i class="fa fa-file-pdf-o mr0"></i>
		</a>
	</h2>
	<p>
		<?php echo __('購入の手通き、見積書・納品書、請求書、領収書の発行方法'); ?><br>
	</p>
</li>

<li>
	<h2>
		<a href="<?php echo Asset::get_file('cl_manual_teacher_Contract.pdf', 'docs'); ?>" target="_blank">
			<?php echo __('個人契約と団体契約の違い') ?> <i class="fa fa-file-pdf-o mr0"></i>
		</a>
	</h2>
	<p>
		<?php echo __('課金体系の違い、団体管理機能について'); ?><br>
	</p>
</li>

</ol>

</div>

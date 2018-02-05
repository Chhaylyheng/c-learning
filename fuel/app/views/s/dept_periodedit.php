<section>
	<?php echo Form::open(array('action'=>'/t/dept/periodedit')) ; ?>
		<h2>期間情報の変更</h2>
		<p>学部の期間情報を入力してください。（※学部共通で利用されます。）</p>
		<p>名称（20文字以内）を空白にすると項目削除になります。</p>
		<p>開始日より終了日が小さい日の場合は年を越えた期間として認識されます。（例：4月1日～3月31日 → <?php echo date('Y'); ?>年4月1日～<?php echo date('Y',strtotime('+1 year')); ?>年3月31日）</p>
		<p>月の上限日を超える値が入力された場合は末日が設定されます。（例：2月31日 → 2月28日）</p>
		<?php if (!is_null($error)): ?>
		<ul>
		<?php foreach ($error as $e): ?>
			<li><?php echo $e; ?></li>
		<?php endforeach; ?>
		</ul>
		<?php endif; ?>
		<table>
		<thead>
			<tr>
				<th>No.</th>
				<th>名称</th>
				<th>期間</th>
			</tr>
		</thead>
		<tbody>
			<tr><td>0</td><td>指定無し</td><td>─</td></tr>
			<?php for ($i = 1; $i < 10; $i++): ?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo Form::input('period_name'.$i,((isset($period[$i]))? $period[$i]['dpName']:''),array('maxlength'=>'20')); ?></td>
					<td>
						<?php echo Form::select('period_s_m'.$i,((isset($period[$i]))? (int)substr($period[$i]['dpStartDate'],0,2):'4'),$month); ?>
						<?php echo Form::select('period_s_d'.$i,((isset($period[$i]))? (int)substr($period[$i]['dpStartDate'],3,2):'1'),$day); ?>
						～
						<?php echo Form::select('period_e_m'.$i,((isset($period[$i]))? (int)substr($period[$i]['dpEndDate'],0,2):'3'),$month); ?>
						<?php echo Form::select('period_e_d'.$i,((isset($period[$i]))? (int)substr($period[$i]['dpEndDate'],3,2):'31'),$day); ?>
					</td>
				</tr>
			<?php endfor; ?>
		</tbody>
		</table>
		<hr />
		<p><?php echo Form::submit('t_submit','更新する'); ?></p>
	<?php echo Form::close(); ?>
</section>

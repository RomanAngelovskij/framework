<?php if (FM\Application::$i->Input->isAjax() === false):?>
	<script>
	$(document).ready(function(){
		$(document).on('click', '.pagination a', function(e){
			e.preventDefault();

			var url = $(this).attr('href');

			$.ajax({
				method: 'GET',
				type: 'html',
				url: url,
				data: {},
				cache: false,
				success: function(data){
					$("#preload").hide(0, function(){
						$("#news").html(data).show();
					});

					history.pushState({}, url, url);
				},
				beforeSend: function(xhr){
					$("#news").hide(0, function(){
						$("#preload").show();
					})
				}
			})
		})
	})
	</script>

	<h1>Новости</h1>
	<p>
	Всего новостей: <?=$this->totalNewsNumber?>
	</p>

	<div id="preload" style="display: none;">
		<div class="progress">
			<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
				<span class="sr-only">Загрузка новостей...</span>
			</div>
		</div>
	</div>

	<div id="news">
<?php endif?>

	<?php if (!empty($this->News)):?>
		<?php if ($this->Pagination->total() > 1):?>
			<nav>
			<ul class="pagination">
				<?php if ($this->Pagination->current() > 1):?>
					<li>
						<a href="/site/index/<?=$this->Pagination->current()-1?>" aria-label="Предыдущая">
							<span aria-hidden="true">&laquo;</span>
						</a>
					</li>
				<?php endif;?>

				<?php
				foreach ($this->Pagination->build(10) as $page):
				?>
					<?php
					$class = $page == $this->Pagination->current() ? 'active' : '';
					?>
					<li class="<?=$class?>">
						<a href="/site/index/<?=$page?>"><?=$page?></a>
					</li>
				<?php endforeach;?>

				<?php if ($this->Pagination->current() < $this->Pagination->total()):?>
					<li>
						<a href="/site/index/<?=$this->Pagination->current()+1?>" aria-label="Следующая">
							<span aria-hidden="true">&raquo;</span>
						</a>
					</li>
				<?php endif;?>
			</ul>
			</nav>
		<?php endif;?>

		<table class="table table-striped">
		<?php foreach ($this->News as $Item):?>
			<tr>
				<td><?=$Item->title?></td>
			</tr>
		<?php endforeach;?>
		</table>

		<?php if ($this->Pagination->total() > 1):?>
			<nav>
				<ul class="pagination">
					<?php if ($this->Pagination->current() > 1):?>
						<li>
							<a href="/site/index/<?=$this->Pagination->current()-1?>" aria-label="Предыдущая">
								<span aria-hidden="true">&laquo;</span>
							</a>
						</li>
					<?php endif;?>

					<?php
					foreach ($this->Pagination->build(10) as $page):
						?>
						<?php
						$class = $page == $this->Pagination->current() ? 'active' : '';
						?>
						<li class="<?=$class?>">
							<a href="/site/index/<?=$page?>"><?=$page?></a>
						</li>
					<?php endforeach;?>

					<?php if ($this->Pagination->current() < $this->Pagination->total()):?>
						<li>
							<a href="/site/index/<?=$this->Pagination->current()+1?>" aria-label="Следующая">
								<span aria-hidden="true">&raquo;</span>
							</a>
						</li>
					<?php endif;?>
				</ul>
			</nav>
		<?php endif;?>
	<?php endif?>
</div>
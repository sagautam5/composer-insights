pest: ## run pest
	./vendor/bin/pest

analyze: ## run analyze
	bin/composer-insights analyze

export_csv: ## run csv export
	bin/composer-insights analyze --export=csv

export_json: ## run json export
	bin/composer-insights analyze --export=json
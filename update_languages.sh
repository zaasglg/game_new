#!/bin/bash

# Список основных файлов для обновления (исключая politics/, aviator*, chicken-road/, backup/)
files=(
    "verification.php"
    "games.php"
    "withdrawal.php"
    "detalization.php"
)

echo "Добавляю французский и арабский языки в основные файлы..."

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "Обрабатываю $file..."
        
        # Создаем временный файл с новыми языками
        cat > temp_languages.txt << 'EOF'
              <div data-translate="footer.french" class="language-dropdown__option" data-lang="fr"
                data-flag="./images/fr.svg">
                <div class="language-dropdown-flag">
                  <img src="./images/fr.svg" alt="FR" />
                </div>
                Français
              </div>
              <div data-translate="footer.arabic" class="language-dropdown__option" data-lang="ar"
                data-flag="./images/ar.svg">
                <div class="language-dropdown-flag">
                  <img src="./images/ar.svg" alt="AR" />
                </div>
                العربية
              </div>
EOF
        
        # Ищем и заменяем первый переключатель (desktop)
        if grep -q 'data-translate="footer.english".*data-lang="en"' "$file"; then
            echo "  - Найден языковой переключатель в $file"
            
            # Используем sed для вставки новых языков после английского
            sed -i.bak '/data-translate="footer\.english".*data-lang="en"/,/English/{
                /English/ {
                    r temp_languages.txt
                }
            }' "$file"
            
            echo "  - Обновлен $file"
        else
            echo "  - Языковой переключатель не найден в $file"
        fi
        
        rm -f temp_languages.txt
    else
        echo "Файл $file не найден"
    fi
done

echo "Готово!"

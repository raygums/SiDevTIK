#!/bin/bash

###############################################################################
# SI Project TIK - Local Deployment Helper
# Main script untuk memudahkan deployment di local environment
###############################################################################

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m'

# Detect if running in Git Bash on Windows
if [[ "$OSTYPE" == "msys" ]] || [[ "$OSTYPE" == "win32" ]]; then
    COMPOSE_DIR="/c/laragon/www/si-project-tik/deployment/local"
    PROJECT_DIR="/c/laragon/www/si-project-tik"
    DOCKER_EXEC="winpty docker exec -it"
    DOCKER_EXEC_SIMPLE="docker exec"
else
    COMPOSE_DIR="$(cd "$(dirname "$0")" && pwd)"
    PROJECT_DIR="$(dirname "$(dirname "$COMPOSE_DIR")")"
    DOCKER_EXEC="docker exec -it"
    DOCKER_EXEC_SIMPLE="docker exec"
fi

# Container names
APP_CONTAINER="si-project-tik-app-dev"

# Function to get WSL gateway IP (Windows host IP)
get_wsl_gateway_ip() {
    if grep -qi microsoft /proc/version 2>/dev/null; then
        # Running in WSL - get gateway IP
        ip route show | grep default | awk '{print $3}'
    else
        # Not WSL, use host.docker.internal
        echo "host.docker.internal"
    fi
}

# Function to update DB_HOST in .env files
update_db_host() {
    local new_ip=$(get_wsl_gateway_ip)
    local project_env="$PROJECT_DIR/.env"
    
    if [ -n "$new_ip" ]; then
        # Update .env file
        if [ -f "$project_env" ]; then
            local current_ip=$(grep "^DB_HOST=" "$project_env" | cut -d'=' -f2)
            if [ "$current_ip" != "$new_ip" ]; then
                sed -i "s/^DB_HOST=.*/DB_HOST=$new_ip/" "$project_env"
                echo -e "${YELLOW}Updated DB_HOST in .env: $current_ip -> $new_ip${NC}"
            fi
        fi
    fi
}

# Function to show menu
show_menu() {
    clear
    echo -e "${CYAN}╔════════════════════════════════════════════════════════╗${NC}"
    echo -e "${CYAN}║                                                        ║${NC}"
    echo -e "${CYAN}║    ${BLUE}SI Project TIK - Local Development Helper${CYAN}       ║${NC}"
    echo -e "${CYAN}║                                                        ║${NC}"
    echo -e "${CYAN}╚════════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "${YELLOW}Pilih operasi:${NC}"
    echo ""
    echo -e "  ${CYAN}--- Start/Stop ---${NC}"
    echo -e "  ${GREEN}1)${NC} Start Development (docker compose up)"
    echo -e "  ${GREEN}2)${NC} Stop Development"
    echo ""
    echo -e "  ${CYAN}--- Rebuild ---${NC}"
    echo -e "  ${GREEN}3)${NC} Clean Rebuild (Hapus semua & rebuild)"
    echo -e "  ${GREEN}4)${NC} Quick Rebuild"
    echo ""
    echo -e "  ${CYAN}--- Status & Logs ---${NC}"
    echo -e "  ${YELLOW}10)${NC} Show Container Status"
    echo -e "  ${YELLOW}11)${NC} Show Logs"
    echo -e "  ${YELLOW}12)${NC} Test Endpoint"
    echo ""
    echo -e "  ${CYAN}--- Laravel Commands ---${NC}"
    echo -e "  ${GREEN}20)${NC} Run Migrations"
    echo -e "  ${GREEN}21)${NC} Fresh Migration with Seed"
    echo -e "  ${GREEN}22)${NC} Clear All Cache"
    echo -e "  ${GREEN}23)${NC} Run Artisan Command"
    echo -e "  ${GREEN}24)${NC} Access App Shell"
    echo ""
    echo -e "  ${CYAN}--- NPM Commands ---${NC}"
    echo -e "  ${GREEN}30)${NC} NPM Install"
    echo -e "  ${GREEN}31)${NC} NPM Run Build"
    echo ""
    echo -e "  ${CYAN}--- Cleanup ---${NC}"
    echo -e "  ${RED}40)${NC} Cleanup Docker Resources"
    echo -e "  ${RED}41)${NC} Remove All Containers"
    echo ""
    echo -e "  ${CYAN}--- WSL/Database ---${NC}"
    echo -e "  ${YELLOW}50)${NC} Auto-Update DB Host IP (WSL Gateway)"
    echo -e "  ${YELLOW}51)${NC} Show Detected Gateway IP"
    echo ""
    echo -e "  ${RED}0)${NC} Exit"
    echo ""
    echo -n "Pilihan [0-51]: "
}

# Function to show container status
show_status() {
    echo ""
    echo -e "${BLUE}Container Status:${NC}"
    echo ""
    docker ps --filter "name=si-project-tik" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
    echo ""
    read -p "Press Enter to continue..."
}

# Function to show logs
show_logs() {
    echo ""
    echo -e "${CYAN}Showing App Logs (Ctrl+C to exit)...${NC}"
    echo ""
    docker logs $APP_CONTAINER --tail 100 -f
}

# Function to test endpoint
test_endpoint() {
    echo ""
    echo -e "${BLUE}Testing Endpoint...${NC}"
    echo ""

    # Load port from .env
    APP_PORT=$(grep "^APP_PORT=" "$COMPOSE_DIR/.env" 2>/dev/null | cut -d'=' -f2 | tr -d '\r')
    APP_PORT=${APP_PORT:-8090}

    echo -n "App Health:       "
    APP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:$APP_PORT 2>/dev/null || echo "000")
    if [ "$APP_STATUS" = "200" ] || [ "$APP_STATUS" = "302" ]; then
        echo -e "${GREEN}✓ $APP_STATUS OK${NC}"
    else
        echo -e "${RED}✗ $APP_STATUS${NC}"
    fi

    echo ""
    echo -e "${YELLOW}URL: http://localhost:$APP_PORT${NC}"
    echo ""
    read -p "Press Enter to continue..."
}

# Function to run artisan command
run_artisan() {
    echo ""
    echo -e "${YELLOW}Masukkan artisan command (contoh: migrate, tinker, route:list):${NC}"
    read -p "php artisan " artisan_cmd

    if [ -n "$artisan_cmd" ]; then
        $DOCKER_EXEC $APP_CONTAINER php artisan $artisan_cmd
    fi

    echo ""
    read -p "Press Enter to continue..."
}

# Check .env file
check_env() {
    if [ ! -f "$COMPOSE_DIR/.env" ]; then
        echo -e "${YELLOW}Creating .env file from .env.example...${NC}"
        cp "$COMPOSE_DIR/.env.example" "$COMPOSE_DIR/.env"
        echo -e "${GREEN}✓ .env file created. Please review and update if needed.${NC}"
        read -p "Press Enter to continue..."
    fi
    return 0
}

# Main loop
while true; do
    show_menu
    read choice

    case $choice in
        # Start/Stop
        1)
            check_env || continue
            echo ""
            update_db_host
            echo -e "${GREEN}Starting Development Environment...${NC}"
            cd "$COMPOSE_DIR"
            docker compose up -d --build
            echo ""

            # Load port from .env
            APP_PORT=$(grep "^APP_PORT=" "$COMPOSE_DIR/.env" 2>/dev/null | cut -d'=' -f2 | tr -d '\r')
            APP_PORT=${APP_PORT:-8090}

            echo -e "${GREEN}✓ Development environment started!${NC}"
            echo -e "${YELLOW}URL: http://localhost:$APP_PORT${NC}"
            read -p "Press Enter to continue..."
            ;;
        2)
            echo ""
            echo -e "${YELLOW}Stopping Development Environment...${NC}"
            cd "$COMPOSE_DIR"
            docker compose down
            echo -e "${GREEN}✓ Development environment stopped!${NC}"
            read -p "Press Enter to continue..."
            ;;

        # Rebuild
        3)
            check_env || continue
            echo ""
            echo -e "${RED}Clean Rebuild - This will remove container and rebuild!${NC}"
            read -p "Are you sure? (y/n): " confirm
            if [ "$confirm" = "y" ] || [ "$confirm" = "Y" ]; then
                update_db_host
                cd "$COMPOSE_DIR"
                docker compose down -v --rmi local
                docker compose up -d --build
                echo -e "${GREEN}✓ Clean rebuild complete!${NC}"
            fi
            read -p "Press Enter to continue..."
            ;;
        4)
            check_env || continue
            echo ""
            update_db_host
            echo -e "${GREEN}Quick Rebuild...${NC}"
            cd "$COMPOSE_DIR"
            docker compose up -d --build
            echo -e "${GREEN}✓ Quick rebuild complete!${NC}"
            read -p "Press Enter to continue..."
            ;;

        # Status & Logs
        10)
            show_status
            ;;
        11)
            show_logs
            ;;
        12)
            test_endpoint
            ;;

        # Laravel Commands
        20)
            echo ""
            echo -e "${GREEN}Running Migrations...${NC}"
            docker exec $APP_CONTAINER php artisan migrate
            echo ""
            read -p "Press Enter to continue..."
            ;;
        21)
            echo ""
            echo -e "${RED}Fresh Migration - This will delete all data!${NC}"
            read -p "Are you sure? (y/n): " confirm
            if [ "$confirm" = "y" ] || [ "$confirm" = "Y" ]; then
                docker exec $APP_CONTAINER php artisan migrate:fresh --seed
            fi
            echo ""
            read -p "Press Enter to continue..."
            ;;
        22)
            echo ""
            echo -e "${CYAN}Clearing All Cache...${NC}"
            docker exec $APP_CONTAINER php artisan optimize:clear 2>/dev/null || echo "App not running"
            echo -e "${GREEN}✓ Cache cleared${NC}"
            echo ""
            read -p "Press Enter to continue..."
            ;;
        23)
            run_artisan
            ;;
        24)
            echo ""
            echo -e "${CYAN}Accessing App Shell...${NC}"
            $DOCKER_EXEC $APP_CONTAINER sh
            ;;

        # NPM Commands
        30)
            echo ""
            echo -e "${GREEN}Running NPM Install...${NC}"
            docker exec $APP_CONTAINER npm install
            echo ""
            read -p "Press Enter to continue..."
            ;;
        31)
            echo ""
            echo -e "${GREEN}Running NPM Build...${NC}"
            docker exec $APP_CONTAINER npm run build
            echo ""
            read -p "Press Enter to continue..."
            ;;

        # Cleanup
        40)
            echo ""
            echo -e "${YELLOW}Cleaning up Docker resources...${NC}"
            echo ""
            docker container prune -f
            docker image prune -f
            docker volume prune -f
            echo ""
            echo -e "${GREEN}✓ Cleanup complete!${NC}"
            docker system df
            echo ""
            read -p "Press Enter to continue..."
            ;;
        41)
            echo ""
            echo -e "${RED}This will remove ALL SI-Project-TIK containers and images!${NC}"
            read -p "Are you sure? (y/n): " confirm
            if [ "$confirm" = "y" ] || [ "$confirm" = "Y" ]; then
                cd "$COMPOSE_DIR"
                docker compose down -v --rmi all 2>/dev/null
                echo -e "${GREEN}✓ All resources removed!${NC}"
            fi
            read -p "Press Enter to continue..."
            ;;

        # WSL/Database
        50)
            echo ""
            echo -e "${CYAN}Auto-Updating DB Host IP...${NC}"
            echo ""
            
            # Get WSL gateway IP
            new_ip=$(ip route show | grep default | awk '{print $3}')
            
            # Fallback if empty
            if [ -z "$new_ip" ]; then
                new_ip="172.25.32.1"
                echo -e "${YELLOW}⚠ Could not detect gateway IP, using default: $new_ip${NC}"
            fi
            
            # Get current IP from .env files
            project_env="$PROJECT_DIR/.env"
            compose_env="$COMPOSE_DIR/.env"
            
            # Debug: Show which files we're checking
            echo -e "${CYAN}Checking files:${NC}"
            echo -e "  - $project_env"
            echo -e "  - $compose_env"
            echo ""
            
            current_ip=$(grep "^DB_HOST=" "$project_env" 2>/dev/null | cut -d'=' -f2)
            
            echo -e "${BLUE}Current DB_HOST in .env: ${YELLOW}'${current_ip}'${NC}"
            echo -e "${BLUE}Detected Gateway IP: ${YELLOW}$new_ip${NC}"
            echo ""
            
            # Check if update needed (empty or different)
            if [ -z "$current_ip" ] || [ "$current_ip" != "$new_ip" ]; then
                echo -e "${YELLOW}Updating DB_HOST to $new_ip...${NC}"
                echo ""
                
                # Update .env di root project
                if [ -f "$project_env" ]; then
                    if grep -q "^DB_HOST=" "$project_env"; then
                        sed -i "s/^DB_HOST=.*/DB_HOST=$new_ip/" "$project_env"
                    else
                        echo "DB_HOST=$new_ip" >> "$project_env"
                    fi
                    echo -e "${GREEN}✓ Updated $PROJECT_DIR/.env${NC}"
                fi
                
                # Note: docker-compose.yml now uses ${DB_HOST} from .env, no need to update
                echo -e "${CYAN}ℹ docker-compose.yml will use DB_HOST from .env files${NC}"
                
                echo ""
                echo -e "${GREEN}✓ All DB_HOST values updated to: ${YELLOW}$new_ip${NC}"
                echo ""
                echo -e "${YELLOW}Restarting containers...${NC}"
                cd "$COMPOSE_DIR"
                docker compose restart
                sleep 3
                docker exec si-project-tik-app-dev php artisan config:cache 2>/dev/null && echo -e "${GREEN}✓ Config cache cleared${NC}"
                echo -e "${GREEN}✓ Containers restarted!${NC}"
            else
                echo -e "${GREEN}✓ DB_HOST is already up-to-date!${NC}"
            fi
            
            echo ""
            read -p "Press Enter to continue..."
            ;;

        51)
            echo ""
            echo -e "${CYAN}╔════════════════════════════════════════════════════════╗${NC}"
            echo -e "${CYAN}║          Detected Gateway IP (WSL/Linux)               ║${NC}"
            echo -e "${CYAN}╚════════════════════════════════════════════════════════╝${NC}"
            echo ""
            
            echo -e "${YELLOW}Detecting IP from system...${NC}"
            echo ""
            
            # Detect gateway IP using the specified command
            detected_ip=$(ip route show | grep default | awk '{print $3}')
            
            if [ -n "$detected_ip" ]; then
                echo -e "${GREEN}✓ Gateway IP Detected:${NC}"
                echo -e "  ${BLUE}IP Address: ${YELLOW}$detected_ip${NC}"
                echo ""
                
                # Show current DB_HOST from .env for comparison
                project_env="$PROJECT_DIR/.env"
                if [ -f "$project_env" ]; then
                    current_db_host=$(grep "^DB_HOST=" "$project_env" 2>/dev/null | cut -d'=' -f2)
                    echo -e "${YELLOW}Current DB_HOST in .env:${NC}"
                    echo -e "  ${BLUE}DB_HOST: ${CYAN}$current_db_host${NC}"
                    echo ""
                    
                    # Compare
                    if [ "$current_db_host" = "$detected_ip" ]; then
                        echo -e "${GREEN}✓ DB_HOST matches detected gateway IP${NC}"
                    elif [ -z "$current_db_host" ]; then
                        echo -e "${RED}⚠ DB_HOST is not set in .env${NC}"
                        echo -e "${YELLOW}  Recommendation: Use menu option 50 to update${NC}"
                    else
                        echo -e "${RED}⚠ DB_HOST differs from detected gateway IP${NC}"
                        echo -e "${YELLOW}  Detected:  $detected_ip${NC}"
                        echo -e "${YELLOW}  In .env:   $current_db_host${NC}"
                        echo -e "${YELLOW}  Recommendation: Use menu option 50 to update${NC}"
                    fi
                fi
            else
                echo -e "${RED}✗ Failed to detect gateway IP${NC}"
                echo -e "${YELLOW}This might not be a WSL/Linux environment${NC}"
            fi
            
            echo ""
            echo -e "${CYAN}Command used: ${NC}ip route show | grep default | awk '{print \$3}'"
            echo ""
            read -p "Press Enter to continue..."
            ;;

        0)
            echo ""
            echo -e "${GREEN}Goodbye!${NC}"
            echo ""
            exit 0
            ;;
        *)
            echo ""
            echo -e "${RED}Invalid choice!${NC}"
            sleep 2
            ;;
    esac
done
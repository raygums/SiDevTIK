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

# Function to update DB_HOST in .env and docker-compose.yml
update_db_host() {
    local new_ip=$(get_wsl_gateway_ip)
    local project_env="$PROJECT_DIR/.env"
    local compose_file="$COMPOSE_DIR/docker-compose.yml"
    
    if [ -n "$new_ip" ]; then
        # Update .env file
        if [ -f "$project_env" ]; then
            local current_ip=$(grep "^DB_HOST=" "$project_env" | cut -d'=' -f2)
            if [ "$current_ip" != "$new_ip" ]; then
                sed -i "s/^DB_HOST=.*/DB_HOST=$new_ip/" "$project_env"
                echo -e "${YELLOW}Updated DB_HOST in .env: $current_ip -> $new_ip${NC}"
            fi
        fi
        
        # Update docker-compose.yml
        if [ -f "$compose_file" ]; then
            sed -i "s/DB_HOST=.*/DB_HOST=$new_ip/" "$compose_file"
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
    echo -e "  ${RED}0)${NC} Exit"
    echo ""
    echo -n "Pilihan [0-41]: "
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

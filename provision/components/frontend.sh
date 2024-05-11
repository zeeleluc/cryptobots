#!/bin/bash

# Update package lists
sudo apt-get update

# Install curl (if not already installed)
sudo apt-get install curl -y

# Install Node Version Manager (NVM) for the vagrant user
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.38.0/install.sh | bash

# Add NVM environment variables to the vagrant user's profile
echo 'export NVM_DIR="$HOME/.nvm"' >> /home/vagrant/.bashrc
echo '[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"' >> /home/vagrant/.bashrc
echo '[ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"' >> /home/vagrant/.bashrc

# Load NVM into the current shell session
export NVM_DIR="/home/vagrant/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"
[ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"

# Install Node.js 18.x as the vagrant user
nvm install 18

# Set Node.js 18.x as the default version for the vagrant user
nvm alias default 18

# Use Node.js 18.x in the current shell session
nvm use 18

# Check if Node.js executable exists
if command -v node &> /dev/null; then
    echo "Node.js installed successfully."
else
    echo "Node.js executable not found. Installation may have failed."
    exit 1
fi

# Install npm (usually comes with Node.js installation)
sudo apt-get install npm -y

# Add Yarn repository and install Yarn
curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | sudo apt-key add -
echo "deb https://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list
sudo apt-get update
sudo apt-get install yarn -y

# Ensure Node.js executable exists
if command -v node &> /dev/null; then
    # Remove existing symbolic links for nodejs and node if they exist
    rm -f /usr/bin/nodejs
    rm -f /usr/bin/node

    # Create new symbolic links for nodejs and node
    ln -s "$(command -v node)" /usr/bin/nodejs
    ln -s "$(command -v node)" /usr/bin/node

    # Ensure correct permissions for node
    chown -R vagrant:vagrant /usr/lib/node_modules
else
    echo "Node.js executable not found after installation. Please check for errors."
    exit 1
fi

# Print versions
echo "Yarn version:"
yarn -v

echo "npm version:"
npm -v

echo "Node.js version:"
node -v

echo "nodejs version:"
nodejs -v

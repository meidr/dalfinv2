const fs = require('fs');
const path = require('path');

function walkDir(dir, callback) {
    fs.readdirSync(dir).forEach(f => {
        let dirPath = path.join(dir, f);
        if (fs.statSync(dirPath).isDirectory()) {
            walkDir(dirPath, callback);
        } else if (dirPath.endsWith('.vue')) {
            callback(dirPath);
        }
    });
}

function processFile(filePath) {
    let content = fs.readFileSync(filePath, 'utf8');
    
    if (!content.includes('<Transition name="modal-fade">')) return;
    
    // Prevent double wrap
    if (content.includes('<Teleport to="body">') && content.indexOf('<Teleport to="body">') < content.indexOf('<Transition name="modal-fade">')) {
        return;
    }

    let lines = content.split('\n');
    let newLines = [];
    let teleportStack = [];
    
    for (let i = 0; i < lines.length; i++) {
        let line = lines[i];
        
        let match = line.match(/^(\s*)<Transition name="modal-fade">/);
        if (match) {
            let indent = match[1];
            newLines.push(`${indent}<Teleport to="body">`);
            newLines.push(line);
            teleportStack.push(indent);
            continue;
        }
        
        if (teleportStack.length > 0) {
            let currentIndent = teleportStack[teleportStack.length - 1];
            let closingMatch = line.match(new RegExp(`^${currentIndent}<\/Transition>`));
            if (closingMatch) {
                newLines.push(line);
                newLines.push(`${currentIndent}</Teleport>`);
                teleportStack.pop();
                continue;
            }
        }
        
        newLines.push(line);
    }
    
    let newContent = newLines.join('\n');
    if (content !== newContent) {
        fs.writeFileSync(filePath, newContent, 'utf8');
        console.log('Updated:', filePath);
    }
}

walkDir(path.join(__dirname, 'frontend/src/views'), processFile);
walkDir(path.join(__dirname, 'frontend/src/components'), processFile);

console.log("Done");

const fs = require('fs');
const path = require('path');

function walkDir(dir, callback) {
    fs.readdirSync(dir).forEach(f => {
        let dirPath = path.join(dir, f);
        let isDirectory = fs.statSync(dirPath).isDirectory();
        if (isDirectory) {
            walkDir(dirPath, callback);
        } else {
            if (dirPath.endsWith('.vue')) {
                callback(dirPath);
            }
        }
    });
}

function processFile(filePath) {
    let content = fs.readFileSync(filePath, 'utf8');
    
    // Check if we have modal-fade transitions
    if (!content.includes('<Transition name="modal-fade">')) return;
    
    // If it already contains Teleport to="body" right before Transition, skip to avoid double wrapping
    if (content.includes('<Teleport to="body">\n      <Transition name="modal-fade">') || 
        content.includes('<Teleport to="body">\n        <Transition name="modal-fade">') ||
        content.includes('<Teleport to="body">\r\n      <Transition name="modal-fade">') ||
        content.includes('<Teleport to="body">\r\n        <Transition name="modal-fade">')) {
        return;
    }

    // We will do a regex replacement. 
    // We want to replace <Transition name="modal-fade"> with <Teleport to="body">\n<Transition name="modal-fade">
    // But we also need to replace the corresponding </Transition> with </Transition>\n</Teleport>
    
    // Because parsing HTML with Regex is hard, and there could be multiple transitions per file, 
    // we can use a simple strategy:
    // 1. Split by `<Transition name="modal-fade">`
    // 2. For each part (except first), find the FIRST `</Transition>` and replace it with `</Transition>\n    </Teleport>`
    
    let parts = content.split(/<Transition name="modal-fade">/);
    if (parts.length > 1) {
        let newContent = parts[0];
        for (let i = 1; i < parts.length; i++) {
            let part = parts[i];
            // Find the closing tag
            // A Modal transition usually contains a single div and then closes.
            // Since there are no nested <Transition> inside a modal-fade, we can just replace the first `</Transition>`
            part = part.replace(/<\/Transition>/, '</Transition>\n    </Teleport>');
            
            // Reattach the opening tag with Teleport
            let indentMatch = parts[i-1].match(/(\s+)$/);
            let indent = indentMatch ? indentMatch[1] : '\n    ';
            
            // We need to keep the exact indentation for Transition, and put Teleport at the same level
            // but for simplicity, we just wrap it.
            newContent += `<Teleport to="body">\n${indent}<Transition name="modal-fade">` + part;
        }
        fs.writeFileSync(filePath, newContent, 'utf8');
        console.log(`Updated: ${filePath}`);
    }
}

walkDir(path.join(__dirname, 'frontend/src/views'), processFile);
walkDir(path.join(__dirname, 'frontend/src/components'), processFile);

console.log("Done");

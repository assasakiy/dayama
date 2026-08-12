import React, { useCallback, forwardRef, useImperativeHandle, useState } from 'react';
import { useEditor, EditorContent, Editor } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import LinkExtension from '@tiptap/extension-link';
import ImageExtension from '@tiptap/extension-image';
import Placeholder from '@tiptap/extension-placeholder';
import {
    Bold, Italic, Strikethrough, Heading2, Heading3,
    List, ListOrdered, Quote, Code, Link as LinkIcon,
    Minus, Image as ImageIcon, Undo, Redo
} from 'lucide-react';

interface TipTapEditorProps {
    content: string;
    onChange: (html: string) => void;
    placeholder?: string;
    onRequestImage?: () => void;
}

const CustomImage = ImageExtension.extend({
    addAttributes() {
        return {
            ...this.parent?.(),
            'data-media-id': {
                default: null,
            },
            'data-src': {
                default: null,
            },
            'class': {
                default: null,
            }
        }
    },
});

export interface TipTapEditorRef {
    insertImage: (thumbUrl: string, fullUrl?: string, mediaId?: number) => void;
}

const TipTapEditor = forwardRef<TipTapEditorRef, TipTapEditorProps>(({ content, onChange, placeholder, onRequestImage }, ref) => {
    const [, forceUpdate] = useState(0);

    const editor = useEditor({
        extensions: [
            StarterKit.configure({
                heading: { levels: [2, 3, 4] },
                link: false,
            }),
            LinkExtension.configure({ openOnClick: false }),
            CustomImage,
            Placeholder.configure({ placeholder: placeholder ?? 'Mulai menulis...' }),
        ],
        content,
        onUpdate: ({ editor }) => onChange(editor.getHTML()),
        onSelectionUpdate: () => forceUpdate(x => x + 1),
        onTransaction: () => forceUpdate(x => x + 1),
        editorProps: {
            attributes: {
                class: 'prose-blog max-w-none focus:outline-none min-h-[400px] px-4 py-4',
            },
        },
    });

    useImperativeHandle(ref, () => ({
        insertImage: (thumbUrl: string, fullUrl?: string, mediaId?: number) => {
            if (editor) {
                editor.chain().focus().setImage({ 
                    src: thumbUrl, 
                    'data-src': fullUrl || thumbUrl,
                    'data-media-id': mediaId,
                    'class': 'lazyload blur-up'
                } as any).run();
            }
        }
    }), [editor]);

    const addLink = useCallback(() => {
        const url = prompt('Masukkan URL:');
        if (url && editor) {
            editor.chain().focus().setLink({ href: url }).run();
        }
    }, [editor]);

    const addImage = useCallback(() => {
        if (onRequestImage) {
            onRequestImage();
        } else {
            const url = prompt('Masukkan URL Gambar:');
            if (url && editor) {
                editor.chain().focus().setImage({ src: url }).run();
            }
        }
    }, [editor, onRequestImage]);

    if (!editor) return null;

    const ToolbarButton = ({ onClick, active, disabled, title, children }: any) => (
        <button
            type="button"
            onMouseDown={(e) => e.preventDefault()}
            onClick={onClick}
            disabled={disabled}
            title={title}
            className={`p-2 rounded-md transition-colors flex items-center justify-center 
                ${disabled ? 'opacity-30 cursor-not-allowed' : ''} 
                ${active ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-surface-muted hover:text-foreground'}`}
        >
            {children}
        </button>
    );

    const Divider = () => <div className="w-px h-6 bg-border-subtle mx-1 self-center" />;

    return (
        <div className="border border-border-subtle rounded-md bg-background shadow-sm focus-within:border-primary focus-within:ring-1 focus-within:ring-primary transition-all relative">
            <div className="flex flex-wrap items-center gap-1 p-1 border-b border-border-subtle bg-surface/80 backdrop-blur-md sticky top-0 z-30 rounded-t-md">
                <ToolbarButton title="Urungkan" onClick={() => editor.chain().focus().undo().run()} disabled={!editor.can().undo()}>
                    <Undo className="w-4 h-4" />
                </ToolbarButton>
                <ToolbarButton title="Ulangi" onClick={() => editor.chain().focus().redo().run()} disabled={!editor.can().redo()}>
                    <Redo className="w-4 h-4" />
                </ToolbarButton>

                <Divider />

                <ToolbarButton title="Tebal" onClick={() => editor.chain().focus().toggleBold().run()} active={editor.isActive('bold')}>
                    <Bold className="w-4 h-4" />
                </ToolbarButton>
                <ToolbarButton title="Miring" onClick={() => editor.chain().focus().toggleItalic().run()} active={editor.isActive('italic')}>
                    <Italic className="w-4 h-4" />
                </ToolbarButton>
                <ToolbarButton title="Coret" onClick={() => editor.chain().focus().toggleStrike().run()} active={editor.isActive('strike')}>
                    <Strikethrough className="w-4 h-4" />
                </ToolbarButton>

                <Divider />

                <ToolbarButton title="Heading 2" onClick={() => editor.chain().focus().toggleHeading({ level: 2 }).run()} active={editor.isActive('heading', { level: 2 })}>
                    <Heading2 className="w-4 h-4" />
                </ToolbarButton>
                <ToolbarButton title="Heading 3" onClick={() => editor.chain().focus().toggleHeading({ level: 3 }).run()} active={editor.isActive('heading', { level: 3 })}>
                    <Heading3 className="w-4 h-4" />
                </ToolbarButton>

                <Divider />

                <ToolbarButton title="Daftar Bullet" onClick={() => editor.chain().focus().toggleBulletList().run()} active={editor.isActive('bulletList')}>
                    <List className="w-4 h-4" />
                </ToolbarButton>
                <ToolbarButton title="Daftar Numbering" onClick={() => editor.chain().focus().toggleOrderedList().run()} active={editor.isActive('orderedList')}>
                    <ListOrdered className="w-4 h-4" />
                </ToolbarButton>
                <ToolbarButton title="Kutipan" onClick={() => editor.chain().focus().toggleBlockquote().run()} active={editor.isActive('blockquote')}>
                    <Quote className="w-4 h-4" />
                </ToolbarButton>
                <ToolbarButton title="Blok Kode" onClick={() => editor.chain().focus().toggleCodeBlock().run()} active={editor.isActive('codeBlock')}>
                    <Code className="w-4 h-4" />
                </ToolbarButton>

                <Divider />

                <ToolbarButton title="Tautan" onClick={addLink} active={editor.isActive('link')}>
                    <LinkIcon className="w-4 h-4" />
                </ToolbarButton>
                <ToolbarButton title="Sisipkan Gambar" onClick={addImage}>
                    <ImageIcon className="w-4 h-4" />
                </ToolbarButton>
                <ToolbarButton title="Garis Horizontal" onClick={() => editor.chain().focus().setHorizontalRule().run()}>
                    <Minus className="w-4 h-4" />
                </ToolbarButton>
            </div>
            
            <div className="bg-background">
                <EditorContent editor={editor} />
            </div>
        </div>
    );
});

export default TipTapEditor;

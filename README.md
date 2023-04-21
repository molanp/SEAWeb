# easyapi_wesbite
一个简单的api模板网页(未完工)

## <a href="https://molanp.github.io/SEAWeb/" target="_blank">帮助文档</a>

## 简单配置

- 要求：PHP>=5.0
- 1. 上传文件至服务器
- 2. 访问`http://{your_website}`进行初次配置
- 3. Run!
- 4. 伪静态设置
    - Apache:无需另行配置，已自带
    - Nginx:
        ```r
        location / {
            if ($query_string ~* (proc/self/environ|GLOBALS(=|[|%[0-9A-Z]{0,2})|_REQUEST(=|[|%[0-9A-Z]{0,2})|(<|%3C).*script.*(\>|%3E)|base64_encode.*\(.*\)|(%24&x)\[.*\].*HTTP|mosConfig_[a-zA-Z_]{1,21}(=|\%3D)|GlobalSettings\[path\]|([\~\`\!\@\$\%\^\&\*\(\)\-\=\+\[\]\{\}\|\\\:\,\.\/\<\>\?\s])|cmd=cd)) {
                return 403;
            }
            rewrite ^/i/(.*)$ /i.php/$1 last;
            try_files $uri $uri/ /index.php?url=$uri&$args;
        }

        error_page 403 /403.php;
        error_page 404 /404.php;

        location = /403.php {
            internal;
        }
        location = /404.php {
            internal;
        }

        ```
    - Internet Information Services (IIS):
        ```r
        <rewrite>
            <rules>
                <rule name="Prevent XSS attacks" stopProcessing="true">
                    <match url=".*" />
                    <conditions logicalGrouping="MatchAny">
                        <add input="{QUERY_STRING}" pattern="proc/self/environ" />
                        <add input="{QUERY_STRING}" pattern="GLOBALS(=|[|%[0-9A-Z]{0,2})" />
                        <add input="{QUERY_STRING}" pattern="_REQUEST(=|[|%[0-9A-Z]{0,2})" />
                        <add input="{QUERY_STRING}" pattern="(&lt;|%3C).*script.*(&gt;|%3E)" />
                        <add input="{QUERY_STRING}" pattern="base64_encode.*\(.*\)" />
                        <add input="{QUERY_STRING}" pattern="(%24&amp;x)\[.*\].*HTTP" />
                        <add input="{QUERY_STRING}" pattern="mosConfig_[a-zA-Z_]{1,21}(=|\%3D)" />
                        <add input="{QUERY_STRING}" pattern="GlobalSettings\[path\]" />
                        <add input="{QUERY_STRING}" pattern="([\~\`\!\@\$\%\^\&amp;\*\(\)\-\=\+\[\]\{\}\|\\\:\,\.\/\<\>\?\s])" />
                        <add input="{QUERY_STRING}" pattern="cmd=cd" />
                    </conditions>
                    <action type="CustomResponse" statusCode="403" statusReason="Forbidden: Access is denied." statusDescription="The requested resource contains invalid parameters." />
                </rule>

                <rule name="Rewrite to i.php">
                    <match url="^i/(.*)" />
                    <action type="Rewrite" url="/i.php/{R:1}" />
                </rule>

                <rule name="Rewrite to index.php">
                    <match url=".*" />
                    <conditions>
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="/index.php?url={REQUEST_URI}&amp;{QUERY_STRING}" />
                </rule>

                <rule name="ErrorDocument 403">
                    <match url="403.php" />
                    <action type="Rewrite" url="/403.php" />
                </rule>

                <rule name="ErrorDocument 404">
                    <match url="404.php" />
                    <action type="Rewrite" url="/404.php" />
                </rule>
            </rules>

            <errorPages>
                <remove statusCode="404" subStatusCode="-1" />
                <remove statusCode="403" subStatusCode="-1" />
                <error statusCode="404" prefixLanguageFilePath="" path="/404.php" responseMode="ExecuteURL" />
                <error statusCode="403" prefixLanguageFilePath="" path="/403.php" responseMode="ExecuteURL" />
            </errorPages>
        </rewrite>
        ```

## 备注

本模板内大部分内容均可在`http://{your_website}/admin`中修改

# 感谢

[parsedown](https://github.com/erusev/parsedown):一个开源的php解析markdown方法